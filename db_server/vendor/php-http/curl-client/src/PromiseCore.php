<?php

declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Shared promises core.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 */
class PromiseCore
{
    /**
     * HTTP request.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * cURL handle.
     *
     * @var resource
     */
    private $handle;

    /**
     * Response builder.
     *
     * @var ResponseBuilder
     */
    private $responseBuilder;

    /**
     * Promise state.
     *
     * @var string
     */
    private $state;

    /**
     * Exception.
     *
     * @var Exception|null
     */
    private $exception = null;

    /**
     * Functions to call when a response will be available.
     *
     * @var callable[]
     */
    private $onFulfilled = [];

    /**
     * Functions to call when an error happens.
     *
     * @var callable[]
     */
    private $onRejected = [];

    /**
     * Create shared core.
     *
     * @param RequestInterface $request HTTP request.
     * @param resource         $handle cURL handle.
     * @param ResponseBuilder  $responseBuilder Response builder.
     *
     * @throws \InvalidArgumentException If $handle is not a cURL resource.
     */
    public function __construct(
        RequestInterface $request,
        $handle,
        ResponseBuilder $responseBuilder
    ) {
        if (!is_resource($handle)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Parameter $handle expected to be a cURL resource, %s given',
                    gettype($handle)
                )
            );
        }

        if (get_resource_type($handle) !== 'curl') {
            throw new \InvalidArgumentException(
                sprintf(
                    'Parameter $handle expected to be a cURL resource, %s resource given',
                    get_resource_type($handle)
                )
            );
        }

        $this->request = $request;
        $this->handle = $handle;
        $this->responseBuilder = $responseBuilder;
        $this->state = Promise::PENDING;
    }

    /**
     * Add on fulfilled callback.
     *
     * @param callable $callback
     */
    public function addOnFulfilled(callable $callback): void
    {
        if ($this->getState() === Promise::PENDING) {
            $this->onFulfilled[] = $callback;
        } elseif ($this->getState() === Promise::FULFILLED) {
            $response = call_user_func($callback, $this->responseBuilder->getResponse());
            if ($response instanceof ResponseInterface) {
                $this->responseBuilder->setResponse($response);
            }
        }
    }

    /**
     * Add on rejected callback.
     *
     * @param callable $callback
     */
    public function addOnRejected(callable $callback): void
    {
        if ($this->getState() === Promise::PENDING) {
            $this->onRejected[] = $callback;
        } elseif ($this->getState() === Promise::REJECTED) {
            $this->exception = call_user_func($callback, $this->exception);
        }
    }

    /**
     * Return cURL handle.
     *
     * @return resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get the state of the promise, one of PENDING, FULFILLED or REJECTED.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Return request.
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Return the value of the promise (fulfilled).
     *
     * @return ResponseInterface Response object only when the Promise is fulfilled
     */
    public function getResponse(): ResponseInterface
    {
        return $this->responseBuilder->getResponse();
    }

    /**
     * Get the reason why the promise was rejected.
     *
     * If the exception is an instance of Http\Client\Exception\HttpException it will contain
     * the response object with the status code and the http reason.
     *
     * @return \Throwable Exception Object only when the Promise is rejected
     *
     * @throws \LogicException When the promise is not rejected
     */
    public function getException(): \Throwable
    {
        if (null === $this->exception) {
            throw new \LogicException('Promise is not rejected');
        }

        return $this->exception;
    }

    /**
     * Fulfill promise.
     */
    public function fulfill(): void
    {
        $this->state = Promise::FULFILLED;
        $response = $this->responseBuilder->getResponse();
        try {
            $response->getBody()->seek(0);
        } catch (\RuntimeException $e) {
            $exception = new Exception\TransferException($e->getMessage(), $e->getCode(), $e);
            $this->reject($exception);

            return;
        }

        while (count($this->onFulfilled) > 0) {
            $callback = array_shift($this->onFulfilled);
            $response = call_user_func($callback, $response);
        }

        if ($response instanceof ResponseInterface) {
            $this->responseBuilder->setResponse($response);
        }
    }

    /**
     * Reject promise.
     *
     * @param Exception $exception Reject reason
     */
    public function reject(Exception $exception): void
    {
        $this->exception = $exception;
        $this->state = Promise::REJECTED;

        while (count($this->onRejected) > 0) {
            $callback = array_shift($this->onRejected);
            try {
                $exception = call_user_func($callback, $this->exception);
                $this->exception = $exception;
            } catch (Exception $exception) {
                $this->exception = $exception;
            }
        }
    }
}
