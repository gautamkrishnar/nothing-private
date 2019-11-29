<?php

declare(strict_types=1);

namespace Sentry\Spool;

use Sentry\Event;
use Sentry\Transport\TransportInterface;

/**
 * This spool stores the events in memory.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class MemorySpool implements SpoolInterface
{
    /**
     * @var Event[] List of enqueued events
     */
    private $events = [];

    /**
     * {@inheritdoc}
     */
    public function queueEvent(Event $event): bool
    {
        $this->events[] = $event;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushQueue(TransportInterface $transport): void
    {
        if (empty($this->events)) {
            return;
        }

        while ($event = array_pop($this->events)) {
            $transport->send($event);
        }
    }
}
