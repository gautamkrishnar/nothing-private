<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Sentry\ErrorHandler;
use Sentry\SentrySdk;

/**
 * This integration hooks into the global error handlers and emits events to
 * Sentry.
 */
final class ExceptionListenerIntegration implements IntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $errorHandler = ErrorHandler::registerOnce(ErrorHandler::DEFAULT_RESERVED_MEMORY_SIZE, false);
        $errorHandler->addExceptionHandlerListener(static function (\Throwable $exception): void {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);

            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if (null === $integration) {
                return;
            }

            $currentHub->captureException($exception);
        });
    }
}
