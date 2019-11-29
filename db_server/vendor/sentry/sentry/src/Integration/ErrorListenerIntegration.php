<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Sentry\ErrorHandler;
use Sentry\Exception\FatalErrorException;
use Sentry\Exception\SilencedErrorException;
use Sentry\Options;
use Sentry\SentrySdk;

/**
 * This integration hooks into the global error handlers and emits events to
 * Sentry.
 */
final class ErrorListenerIntegration implements IntegrationInterface
{
    /**
     * @var Options|null The options, to know which error level to use
     */
    private $options;

    /**
     * @var bool Whether to handle fatal errors or not
     */
    private $handleFatalErrors;

    /**
     * Constructor.
     *
     * @param Options|null $options           The options to be used with this integration
     * @param bool         $handleFatalErrors Whether to handle fatal errors or not
     */
    public function __construct(?Options $options = null, bool $handleFatalErrors = true)
    {
        if (null !== $options) {
            @trigger_error(sprintf('Passing the options as argument of the constructor of the "%s" class is deprecated since version 2.1 and will not work in 3.0.', self::class), E_USER_DEPRECATED);
        }

        if ($handleFatalErrors) {
            @trigger_error(sprintf('Handling fatal errors with the "%s" class is deprecated since version 2.1. Use the "%s" integration instead.', self::class, FatalErrorListenerIntegration::class), E_USER_DEPRECATED);
        }

        $this->options = $options;
        $this->handleFatalErrors = $handleFatalErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        /** @psalm-suppress DeprecatedMethod */
        $errorHandler = ErrorHandler::registerOnce(ErrorHandler::DEFAULT_RESERVED_MEMORY_SIZE, false);
        $errorHandler->addErrorHandlerListener(function (\ErrorException $exception): void {
            if (!$this->handleFatalErrors && $exception instanceof FatalErrorException) {
                return;
            }

            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();

            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if (null === $integration || null === $client) {
                return;
            }

            $options = $this->options ?? $client->getOptions();

            if ($exception instanceof SilencedErrorException && !$options->shouldCaptureSilencedErrors()) {
                return;
            }

            if (!($options->getErrorTypes() & $exception->getSeverity())) {
                return;
            }

            $currentHub->captureException($exception);
        });
    }
}
