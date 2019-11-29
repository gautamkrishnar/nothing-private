<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sentry\Event;
use Sentry\Exception\JsonException;
use Sentry\Options;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Sentry\Util\JSON;
use Zend\Diactoros\ServerRequestFactory;

/**
 * This integration collects information from the request and attaches them to
 * the event.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class RequestIntegration implements IntegrationInterface
{
    /**
     * This constant represents the size limit in bytes beyond which the body
     * of the request is not captured when the `max_request_body_size` option
     * is set to `small`.
     */
    private const REQUEST_BODY_SMALL_MAX_CONTENT_LENGTH = 10 ** 3;

    /**
     * This constant represents the size limit in bytes beyond which the body
     * of the request is not captured when the `max_request_body_size` option
     * is set to `medium`.
     */
    private const REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH = 10 ** 4;

    /**
     * @var Options|null The client options
     */
    private $options;

    /**
     * Constructor.
     *
     * @param Options $options The client options
     */
    public function __construct(?Options $options = null)
    {
        if (null !== $options) {
            @trigger_error(sprintf('Passing the options as argument of the constructor of the "%s" class is deprecated since version 2.1 and will not work in 3.0.', self::class), E_USER_DEPRECATED);
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(function (Event $event): Event {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();

            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if (null === $integration || null === $client) {
                return $event;
            }

            $this->processEvent($event, $this->options ?? $client->getOptions());

            return $event;
        });
    }

    /**
     * Applies the information gathered by the this integration to the event.
     *
     * @param self                        $self    The current instance of the integration
     * @param Event                       $event   The event that will be enriched with a request
     * @param ServerRequestInterface|null $request The Request that will be processed and added to the event
     */
    public static function applyToEvent(self $self, Event $event, ?ServerRequestInterface $request = null): void
    {
        @trigger_error(sprintf('The "%s" method is deprecated since version 2.1 and will be removed in 3.0.', __METHOD__), E_USER_DEPRECATED);

        if (null === $self->options) {
            throw new \BadMethodCallException('The options of the integration must be set.');
        }

        $self->processEvent($event, $self->options, $request);
    }

    private function processEvent(Event $event, Options $options, ?ServerRequestInterface $request = null): void
    {
        if (null === $request) {
            $request = isset($_SERVER['REQUEST_METHOD']) && \PHP_SAPI !== 'cli' ? ServerRequestFactory::fromGlobals() : null;
        }

        if (null === $request) {
            return;
        }

        $requestData = [
            'url' => (string) $request->getUri(),
            'method' => $request->getMethod(),
        ];

        if ($request->getUri()->getQuery()) {
            $requestData['query_string'] = $request->getUri()->getQuery();
        }

        if ($options->shouldSendDefaultPii()) {
            $serverParams = $request->getServerParams();

            if (isset($serverParams['REMOTE_ADDR'])) {
                $requestData['env']['REMOTE_ADDR'] = $serverParams['REMOTE_ADDR'];
            }

            $requestData['cookies'] = $request->getCookieParams();
            $requestData['headers'] = $request->getHeaders();

            $userContext = $event->getUserContext();

            if (null === $userContext->getIpAddress() && isset($serverParams['REMOTE_ADDR'])) {
                $userContext->setIpAddress($serverParams['REMOTE_ADDR']);
            }
        } else {
            $requestData['headers'] = $this->removePiiFromHeaders($request->getHeaders());
        }

        $requestBody = $this->captureRequestBody($options, $request);

        if (!empty($requestBody)) {
            $requestData['data'] = $requestBody;
        }

        $event->setRequest($requestData);
    }

    /**
     * Removes headers containing potential PII.
     *
     * @param array<string, array<int, string>> $headers Array containing request headers
     *
     * @return array<string, array<int, string>>
     */
    private function removePiiFromHeaders(array $headers): array
    {
        $keysToRemove = ['authorization', 'cookie', 'set-cookie', 'remote_addr'];

        return array_filter(
            $headers,
            static function (string $key) use ($keysToRemove): bool {
                return !\in_array(strtolower($key), $keysToRemove, true);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Gets the decoded body of the request, if available. If the Content-Type
     * header contains "application/json" then the content is decoded and if
     * the parsing fails then the raw data is returned. If there are submitted
     * fields or files, all of their information are parsed and returned.
     *
     * @param Options                $options       The options of the client
     * @param ServerRequestInterface $serverRequest The server request
     *
     * @return mixed
     */
    private function captureRequestBody(Options $options, ServerRequestInterface $serverRequest)
    {
        $maxRequestBodySize = $options->getMaxRequestBodySize();
        $requestBody = $serverRequest->getBody();

        if (
            'none' === $maxRequestBodySize ||
            ('small' === $maxRequestBodySize && $requestBody->getSize() > self::REQUEST_BODY_SMALL_MAX_CONTENT_LENGTH) ||
            ('medium' === $maxRequestBodySize && $requestBody->getSize() > self::REQUEST_BODY_MEDIUM_MAX_CONTENT_LENGTH)
        ) {
            return null;
        }

        $requestData = $serverRequest->getParsedBody();
        $requestData = array_merge(
            $this->parseUploadedFiles($serverRequest->getUploadedFiles()),
            \is_array($requestData) ? $requestData : []
        );

        if (!empty($requestData)) {
            return $requestData;
        }

        if ('application/json' === $serverRequest->getHeaderLine('Content-Type')) {
            try {
                return JSON::decode($requestBody->getContents());
            } catch (JsonException $exception) {
                // Fallback to returning the raw data from the request body
            }
        }

        return $requestBody->getContents();
    }

    /**
     * Create an array with the same structure as $uploadedFiles, but replacing
     * each UploadedFileInterface with an array of info.
     *
     * @param array $uploadedFiles The uploaded files info from a PSR-7 server request
     */
    private function parseUploadedFiles(array $uploadedFiles): array
    {
        $result = [];

        foreach ($uploadedFiles as $key => $item) {
            if ($item instanceof UploadedFileInterface) {
                $result[$key] = [
                    'client_filename' => $item->getClientFilename(),
                    'client_media_type' => $item->getClientMediaType(),
                    'size' => $item->getSize(),
                ];
            } elseif (\is_array($item)) {
                $result[$key] = $this->parseUploadedFiles($item);
            } else {
                throw new \UnexpectedValueException(sprintf('Expected either an object implementing the "%s" interface or an array. Got: "%s".', UploadedFileInterface::class, \is_object($item) ? \get_class($item) : \gettype($item)));
            }
        }

        return $result;
    }
}
