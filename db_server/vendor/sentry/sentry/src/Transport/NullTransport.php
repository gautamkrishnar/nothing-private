<?php

declare(strict_types=1);

namespace Sentry\Transport;

use Sentry\Event;

/**
 * This transport fakes the sending of events by just ignoring them.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class NullTransport implements TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function send(Event $event): ?string
    {
        return $event->getId();
    }
}
