<?php

declare(strict_types=1);

namespace Sentry\Transport;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use Http\Client\HttpAsyncClient as HttpAsyncClientInterface;
use Http\Message\RequestFactory as RequestFactoryInterface;
use Http\Promise\Promise as HttpPromiseInterface;
use Sentry\Event;
use Sentry\Exception\MissingProjectIdCredentialException;
use Sentry\Options;
use Sentry\Util\JSON;

/**
 * This transport sends the events using a syncronous HTTP client that will
 * delay sending of the requests until the shutdown of the application.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class HttpTransport implements TransportInterface, ClosableTransportInterface
{
    /**
     * @var Options The Raven client configuration
     */
    private $config;

    /**
     * @var HttpAsyncClientInterface The HTTP client
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface The PSR-7 request factory
     */
    private $requestFactory;

    /**
     * @var HttpPromiseInterface[] The list of pending promises
     */
    private $pendingRequests = [];

    /**
     * @var bool Flag indicating whether the sending of the events should be
     *           delayed until the shutdown of the application
     */
    private $delaySendingUntilShutdown = false;

    /**
     * Constructor.
     *
     * @param Options                  $config                    The Raven client configuration
     * @param HttpAsyncClientInterface $httpClient                The HTTP client
     * @param RequestFactoryInterface  $requestFactory            The PSR-7 request factory
     * @param bool                     $delaySendingUntilShutdown This flag controls whether to delay
     *                                                            sending of the events until the shutdown
     *                                                            of the application
     * @param bool                     $triggerDeprecation        Flag controlling whether to throw
     *                                                            a deprecation if the transport is
     *                                                            used relying on the deprecated behavior
     *                                                            of delaying the sending of the events
     *                                                            until the shutdown of the application
     */
    public function __construct(
        Options $config,
        HttpAsyncClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        bool $delaySendingUntilShutdown = true,
        bool $triggerDeprecation = true
    ) {
        if ($delaySendingUntilShutdown && $triggerDeprecation) {
            @trigger_error(sprintf('Delaying the sending of the events using the "%s" class is deprecated since version 2.2 and will not work in 3.0.', __CLASS__), E_USER_DEPRECATED);
        }

        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->delaySendingUntilShutdown = $delaySendingUntilShutdown;

        // By calling the cleanupPendingRequests function from a shutdown function
        // registered inside another shutdown function we can be confident that it
        // will be executed last
        register_shutdown_function('register_shutdown_function', \Closure::fromCallable([$this, 'cleanupPendingRequests']));
    }

    /**
     * Destructor. Ensures that all pending requests ends before destroying this
     * object instance.
     */
    public function __destruct()
    {
        $this->cleanupPendingRequests();
    }

    /**
     * {@inheritdoc}
     */
    public function send(Event $event): ?string
    {
        $projectId = $this->config->getProjectId();

        if (null === $projectId) {
            throw new MissingProjectIdCredentialException();
        }

        $request = $this->requestFactory->createRequest(
            'POST',
            sprintf('/api/%d/store/', $projectId),
            ['Content-Type' => 'application/json'],
            JSON::encode($event)
        );

        $promise = $this->httpClient->sendAsyncRequest($request);

        if ($this->delaySendingUntilShutdown) {
            $this->pendingRequests[] = $promise;
        } else {
            try {
                $promise->wait();
            } catch (\Throwable $exception) {
                return null;
            }
        }

        return $event->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function close(?int $timeout = null): PromiseInterface
    {
        $this->cleanupPendingRequests();

        return new FulfilledPromise(true);
    }

    /**
     * Cleanups the pending promises by awaiting for them. Any error that occurs
     * will be ignored.
     *
     * @deprecated since version 2.2.3, to be removed in 3.0. Even though this
     *             method is `private` we cannot delete it because it's used
     *             in some old versions of the `sentry-laravel` package using
     *             tricky code involving reflection and Closure binding
     */
    private function cleanupPendingRequests(): void
    {
        while ($promise = array_pop($this->pendingRequests)) {
            try {
                $promise->wait();
            } catch (\Throwable $exception) {
                // Do nothing because we don't want to break applications while
                // trying to send events
            }
        }
    }
}
