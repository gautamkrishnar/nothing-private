<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Integration\IntegrationInterface;
use Sentry\State\Scope;

/**
 * This interface must be implemented by all Raven client classes.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
interface ClientInterface
{
    /**
     * Returns the options of the client.
     */
    public function getOptions(): Options;

    /**
     * Logs a message.
     *
     * @param string     $message The message (primary description) for the event
     * @param Severity   $level   The level of the message to be sent
     * @param Scope|null $scope   An optional scope keeping the state
     */
    public function captureMessage(string $message, ?Severity $level = null, ?Scope $scope = null): ?string;

    /**
     * Logs an exception.
     *
     * @param \Throwable $exception The exception object
     * @param Scope|null $scope     An optional scope keeping the state
     */
    public function captureException(\Throwable $exception, ?Scope $scope = null): ?string;

    /**
     * Logs the most recent error (obtained with {@link error_get_last}).
     *
     * @param Scope|null $scope An optional scope keeping the state
     */
    public function captureLastError(?Scope $scope = null): ?string;

    /**
     * Captures a new event using the provided data.
     *
     * @param array      $payload The data of the event being captured
     * @param Scope|null $scope   An optional scope keeping the state
     */
    public function captureEvent(array $payload, ?Scope $scope = null): ?string;

    /**
     * Returns the integration instance if it is installed on the Client.
     *
     * @param string $className the classname of the integration
     */
    public function getIntegration(string $className): ?IntegrationInterface;
}
