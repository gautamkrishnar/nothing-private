<?php

declare(strict_types=1);

namespace Sentry\Exception;

use Throwable;

/**
 * This exception is thrown during the sending of an event when the public key
 * is not provided in the DSN.
 */
final class MissingPublicKeyCredentialException extends \RuntimeException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('The public key of the DSN is required to authenticate with the Sentry server.', 0, $previous);
    }
}
