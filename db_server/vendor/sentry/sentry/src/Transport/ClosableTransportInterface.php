<?php

declare(strict_types=1);

namespace Sentry\Transport;

use GuzzleHttp\Promise\PromiseInterface;

/**
 * All classes implementing this interface will support sending events on-demand.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
interface ClosableTransportInterface
{
    /**
     * Waits until all pending requests have been sent or the timeout expires.
     *
     * @param int|null $timeout Maximum time in seconds before the sending
     *                          operation is interrupted
     */
    public function close(?int $timeout = null): PromiseInterface;
}
