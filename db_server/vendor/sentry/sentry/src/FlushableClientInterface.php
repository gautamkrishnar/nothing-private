<?php

declare(strict_types=1);

namespace Sentry;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * This interface can be implemented by clients to support flushing of events
 * on-demand.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
interface FlushableClientInterface extends ClientInterface
{
    /**
     * Flushes the queue of events pending to be sent. If a timeout is provided
     * and the queue takes longer to drain, the promise resolves with `false`.
     *
     * @param int|null $timeout Maximum time in seconds the client should wait
     */
    public function flush(?int $timeout = null): PromiseInterface;
}
