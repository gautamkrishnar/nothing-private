<?php

declare(strict_types=1);

namespace Sentry\Transport;

use Sentry\Event;
use Sentry\Exception\MissingProjectIdCredentialException;

/**
 * This interface must be implemented by all classes willing to provide a way
 * of sending events to a Sentry server.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
interface TransportInterface
{
    /**
     * Sends the given event.
     *
     * @param Event $event The event
     *
     * @return string|null Returns the ID of the event or `null` if it failed to be sent
     *
     * @throws MissingProjectIdCredentialException If the project ID is missing in the DSN
     */
    public function send(Event $event): ?string;
}
