<?php

declare(strict_types=1);

namespace Sentry\Integration;

use Sentry\ErrorHandler;
use Sentry\Exception\FatalErrorException;
use Sentry\Options;
use Sentry\SentrySdk;

/**
 * This integration hooks into the error handler and captures fatal errors.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class FatalErrorListenerIntegration implements IntegrationInterface
{
    /**
     * @var Options|null The options, to know which error level to use
     */
    private $options;

    /**
     * Constructor.
     *
     * @param Options|null $options The options to be used with this integration
     */
    public function __construct(?Options $options = null)
    {
        if (null !== $options) {
            @trigger_error(sprintf('Passing the options as argument of the constructor of the "%s" class is deprecated since version 2.1 and will not work in 3.0.', self::class), E_USER_DEPRECATED);
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setupOnce(): void
    {
        $errorHandler = ErrorHandler::registerOnceFatalErrorHandler();
        $errorHandler->addFatalErrorHandlerListener(function (FatalErrorException $exception): void {
            $currentHub = SentrySdk::getCurrentHub();
            $integration = $currentHub->getIntegration(self::class);
            $client = $currentHub->getClient();

            // The client bound to the current hub, if any, could not have this
            // integration enabled. If this is the case, bail out
            if (null === $integration || null === $client) {
                return;
            }

            $options = $this->options ?? $client->getOptions();

            if (!($options->getErrorTypes() & $exception->getSeverity())) {
                return;
            }

            $currentHub->captureException($exception);
        });
    }
}
