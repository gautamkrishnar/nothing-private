<?php

declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Client\Exception;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PSR-18 and HTTPlug Async client based on lib-curl.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 * @author  Blake Williams <github@shabbyrobe.org>
 *
 * @api
 *
 * @since   1.0
 */
class Client implements HttpClient, HttpAsyncClient
{
    /**
     * cURL options.
     *
     * @var array
     */
    private $curlOptions;

    /**
     * PSR-17 response factory.
     *
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * PSR-17 stream factory.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * cURL synchronous requests handle.
     *
     * @var resource|null
     */
    private $handle;

    /**
     * Simultaneous requests runner.
     *
     * @var MultiRunner|null
     */
    private $multiRunner;

    /**
     * Create HTTP client.
     *
     * @param ResponseFactoryInterface|null $responseFactory PSR-17 HTTP response factory.
     * @param StreamFactoryInterface|null   $streamFactory   PSR-17 HTTP stream factory.
     * @param array                         $options         cURL options
     *                                                       {@link http://php.net/curl_setopt}.
     *
     * @throws NotFoundException If factory discovery failed.
     *
     * @since 2.0 Accepts PSR-17 factories instead of HTTPlug ones.
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory = null,
        StreamFactoryInterface $streamFactory = null,
        array $options = []
    ) {
        $this->responseFactory = $responseFactory ?: Psr17FactoryDiscovery::findResponseFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_FOLLOWLOCATION => false
            ]
        );

        // Our parsing will fail if this is set to true.
        $resolver->setAllowedValues(
            CURLOPT_HEADER,
            [false]
        );

        // Our parsing will fail if this is set to true.
        $resolver->setAllowedValues(
            CURLOPT_RETURNTRANSFER,
            [false]
        );

        // We do not know what everything curl supports and might support in the future.
        // Make sure that we accept everything that is in the options.
        $resolver->setDefined(array_keys($options));

        $this->curlOptions = $resolver->resolve($options);
    }

    /**
     * Release resources if still active.
     */
    public function __destruct()
    {
        if (is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }

    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \InvalidArgumentException  For invalid header names or values.
     * @throws \RuntimeException          If creating the body stream fails.
     * @throws Exception\NetworkException In case of network problems.
     * @throws Exception\RequestException On invalid request.
     *
     * @since 1.6 \UnexpectedValueException replaced with RequestException
     * @since 1.6 Throw NetworkException on network errors
     * @since 1.0
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $responseBuilder = $this->createResponseBuilder();
        $requestOptions = $this->prepareRequestOptions($request, $responseBuilder);

        if (is_resource($this->handle)) {
            curl_reset($this->handle);
        } else {
            $this->handle = curl_init();
        }

        curl_setopt_array($this->handle, $requestOptions);
        curl_exec($this->handle);

        $errno = curl_errno($this->handle);
        switch ($errno) {
            case CURLE_OK:
                // All OK, no actions needed.
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new Exception\NetworkException(curl_error($this->handle), $request);
            default:
                throw new Exception\RequestException(curl_error($this->handle), $request);
        }

        $response = $responseBuilder->getResponse();
        $response->getBody()->seek(0);

        return $response;
    }

    /**
     * Create builder to use for building response object.
     *
     * @return ResponseBuilder
     */
    private function createResponseBuilder(): ResponseBuilder
    {
        $body = $this->streamFactory->createStreamFromFile('php://temp', 'w+b');

        $response = $this->responseFactory
            ->createResponse(200)
            ->withBody($body);

        return new ResponseBuilder($response);
    }

    /**
     * Update cURL options for given request and hook in the response builder.
     *
     * @param RequestInterface $request         Request on which to create options.
     * @param ResponseBuilder  $responseBuilder Builder to use for building response.
     *
     * @return array cURL options based on request.
     *
     * @throws \InvalidArgumentException  For invalid header names or values.
     * @throws \RuntimeException          If can not read body.
     * @throws Exception\RequestException On invalid request.
     */
    private function prepareRequestOptions(
        RequestInterface $request,
        ResponseBuilder $responseBuilder
    ): array {
        $curlOptions = $this->curlOptions;

        try {
            $curlOptions[CURLOPT_HTTP_VERSION]
                = $this->getProtocolVersion($request->getProtocolVersion());
        } catch (\UnexpectedValueException $e) {
            throw new Exception\RequestException($e->getMessage(), $request);
        }
        $curlOptions[CURLOPT_URL] = (string)$request->getUri();

        $curlOptions = $this->addRequestBodyOptions($request, $curlOptions);

        $curlOptions[CURLOPT_HTTPHEADER] = $this->createHeaders($request, $curlOptions);

        if ($request->getUri()->getUserInfo()) {
            $curlOptions[CURLOPT_USERPWD] = $request->getUri()->getUserInfo();
        }

        $curlOptions[CURLOPT_HEADERFUNCTION] = function ($ch, $data) use ($responseBuilder) {
            $str = trim($data);
            if ('' !== $str) {
                if (stripos($str, 'http/') === 0) {
                    $responseBuilder->setStatus($str)->getResponse();
                } else {
                    $responseBuilder->addHeader($str);
                }
            }

            return strlen($data);
        };

        $curlOptions[CURLOPT_WRITEFUNCTION] = function ($ch, $data) use ($responseBuilder) {
            return $responseBuilder->getResponse()->getBody()->write($data);
        };

        return $curlOptions;
    }

    /**
     * Return cURL constant for specified HTTP version.
     *
     * @param string $requestVersion HTTP version ("1.0", "1.1" or "2.0").
     *
     * @return int Respective CURL_HTTP_VERSION_x_x constant.
     *
     * @throws \UnexpectedValueException If unsupported version requested.
     */
    private function getProtocolVersion(string $requestVersion): int
    {
        switch ($requestVersion) {
            case '1.0':
                return CURL_HTTP_VERSION_1_0;
            case '1.1':
                return CURL_HTTP_VERSION_1_1;
            case '2.0':
                if (defined('CURL_HTTP_VERSION_2_0')) {
                    return CURL_HTTP_VERSION_2_0;
                }
                throw new \UnexpectedValueException('libcurl 7.33 needed for HTTP 2.0 support');
        }

        return CURL_HTTP_VERSION_NONE;
    }

    /**
     * Add request body related cURL options.
     *
     * @param RequestInterface $request     Request on which to create options.
     * @param array            $curlOptions Options created by prepareRequestOptions().
     *
     * @return array cURL options based on request.
     */
    private function addRequestBodyOptions(RequestInterface $request, array $curlOptions): array
    {
        /*
         * Some HTTP methods cannot have payload:
         *
         * - GET — cURL will automatically change method to PUT or POST if we set CURLOPT_UPLOAD or
         *   CURLOPT_POSTFIELDS.
         * - HEAD — cURL treats HEAD as GET request with a same restrictions.
         * - TRACE — According to RFC7231: a client MUST NOT send a message body in a TRACE request.
         */
        if (!in_array($request->getMethod(), ['GET', 'HEAD', 'TRACE'], true)) {
            $body = $request->getBody();
            $bodySize = $body->getSize();
            if ($bodySize !== 0) {
                if ($body->isSeekable()) {
                    $body->rewind();
                }

                // Message has non empty body.
                if (null === $bodySize || $bodySize > 1024 * 1024) {
                    // Avoid full loading large or unknown size body into memory
                    $curlOptions[CURLOPT_UPLOAD] = true;
                    if (null !== $bodySize) {
                        $curlOptions[CURLOPT_INFILESIZE] = $bodySize;
                    }
                    $curlOptions[CURLOPT_READFUNCTION] = function ($ch, $fd, $length) use ($body) {
                        return $body->read($length);
                    };
                } else {
                    // Small body can be loaded into memory
                    $curlOptions[CURLOPT_POSTFIELDS] = (string)$body;
                }
            }
        }

        if ($request->getMethod() === 'HEAD') {
            // This will set HTTP method to "HEAD".
            $curlOptions[CURLOPT_NOBODY] = true;
        } elseif ($request->getMethod() !== 'GET') {
            // GET is a default method. Other methods should be specified explicitly.
            $curlOptions[CURLOPT_CUSTOMREQUEST] = $request->getMethod();
        }

        return $curlOptions;
    }

    /**
     * Create headers array for CURLOPT_HTTPHEADER.
     *
     * @param RequestInterface $request     Request on which to create headers.
     * @param array            $curlOptions Options created by prepareRequestOptions().
     *
     * @return string[]
     */
    private function createHeaders(RequestInterface $request, array $curlOptions): array
    {
        $curlHeaders = [];
        $headers = $request->getHeaders();
        foreach ($headers as $name => $values) {
            $header = strtolower($name);
            if ('expect' === $header) {
                // curl-client does not support "Expect-Continue", so dropping "expect" headers
                continue;
            }
            if ('content-length' === $header) {
                if (array_key_exists(CURLOPT_POSTFIELDS, $curlOptions)) {
                    // Small body content length can be calculated here.
                    $values = [strlen($curlOptions[CURLOPT_POSTFIELDS])];
                } elseif (!array_key_exists(CURLOPT_READFUNCTION, $curlOptions)) {
                    // Else if there is no body, forcing "Content-length" to 0
                    $values = [0];
                }
            }
            foreach ($values as $value) {
                $curlHeaders[] = $name . ': ' . $value;
            }
        }
        /*
         * curl-client does not support "Expect-Continue", but cURL adds "Expect" header by default.
         * We can not suppress it, but we can set it to empty.
         */
        $curlHeaders[] = 'Expect:';

        return $curlHeaders;
    }

    /**
     * Sends a PSR-7 request in an asynchronous way.
     *
     * Exceptions related to processing the request are available from the returned Promise.
     *
     * @param RequestInterface $request
     *
     * @return Promise Resolves a PSR-7 Response or fails with an Http\Client\Exception.
     *
     * @throws \InvalidArgumentException  For invalid header names or values.
     * @throws \RuntimeException          If creating the body stream fails.
     * @throws Exception\RequestException On invalid request.
     *
     * @since 1.6 \UnexpectedValueException replaced with RequestException
     * @since 1.0
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        if (!$this->multiRunner instanceof MultiRunner) {
            $this->multiRunner = new MultiRunner();
        }

        $handle = curl_init();
        $responseBuilder = $this->createResponseBuilder();
        $requestOptions = $this->prepareRequestOptions($request, $responseBuilder);
        curl_setopt_array($handle, $requestOptions);

        $core = new PromiseCore($request, $handle, $responseBuilder);
        $promise = new CurlPromise($core, $this->multiRunner);
        $this->multiRunner->add($core);

        return $promise;
    }
}
