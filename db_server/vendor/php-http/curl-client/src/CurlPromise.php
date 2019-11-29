<?php

declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Promise\Promise;

/**
 * Promise represents a response that may not be available yet, but will be resolved at some point
 * in future. It acts like a proxy to the actual response.
 *
 * This interface is an extension of the promises/a+ specification https://promisesaplus.com/
 * Value is replaced by an object where its class implement a Psr\Http\Message\RequestInterface.
 * Reason is replaced by an object where its class implement a Http\Client\Exception.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 */
class CurlPromise implements Promise
{
    /**
     * Shared promise core.
     *
     * @var PromiseCore
     */
    private $core;

    /**
     * Requests runner.
     *
     * @var MultiRunner
     */
    private $runner;

    /**
     * Create new promise.
     *
     * @param PromiseCore $core   Shared promise core
     * @param MultiRunner $runner Simultaneous requests runner
     */
    public function __construct(PromiseCore $core, MultiRunner $runner)
    {
        $this->core = $core;
        $this->runner = $runner;
    }

    /**
     * Add behavior for when the promise is resolved or rejected.
     *
     * If you do not care about one of the cases, you can set the corresponding callable to null
     * The callback will be called when the response or exception arrived and never more than once.
     *
     * @param callable $onFulfilled Called when a response will be available
     * @param callable $onRejected  Called when an error happens.
     *
     * You must always return the Response in the interface or throw an Exception
     *
     * @return Promise Always returns a new promise which is resolved with value of the executed
     *                 callback (onFulfilled / onRejected)
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if ($onFulfilled) {
            $this->core->addOnFulfilled($onFulfilled);
        }
        if ($onRejected) {
            $this->core->addOnRejected($onRejected);
        }

        return new self($this->core, $this->runner);
    }

    /**
     * Get the state of the promise, one of PENDING, FULFILLED or REJECTED.
     *
     * @return string
     */
    public function getState()
    {
        return $this->core->getState();
    }

    /**
     * Wait for the promise to be fulfilled or rejected.
     *
     * When this method returns, the request has been resolved and the appropriate callable has terminated.
     *
     * When called with the unwrap option
     *
     * @param bool $unwrap Whether to return resolved value / throw reason or not
     *
     * @return \Psr\Http\Message\ResponseInterface|null Resolved value, null if $unwrap is set to false
     *
     * @throws \Http\Client\Exception The rejection reason
     */
    public function wait($unwrap = true)
    {
        $this->runner->wait($this->core);

        if ($unwrap) {
            if ($this->core->getState() === self::REJECTED) {
                throw $this->core->getException();
            }

            return $this->core->getResponse();
        }

        return null;
    }
}
