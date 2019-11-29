<?php

declare(strict_types=1);

namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\Integration\IntegrationInterface;
use Sentry\SentrySdk;
use Sentry\Severity;

/**
 * This interface represent the class which is responsible for maintaining a
 * stack of pairs of clients and scopes. It is the main entry point to talk
 * with the Sentry client.
 */
interface HubInterface
{
    /**
     * Gets the client bound to the top of the stack.
     */
    public function getClient(): ?ClientInterface;

    /**
     * Gets the ID of the last captured event.
     */
    public function getLastEventId(): ?string;

    /**
     * Creates a new scope to store context information that will be layered on
     * top of the current one. It is isolated, i.e. all breadcrumbs and context
     * information added to this scope will be removed once the scope ends. Be
     * sure to always remove this scope with {@see Hub::popScope} when the
     * operation finishes or throws.
     */
    public function pushScope(): Scope;

    /**
     * Removes a previously pushed scope from the stack. This restores the state
     * before the scope was pushed. All breadcrumbs and context information added
     * since the last call to {@see Hub::pushScope} are discarded.
     */
    public function popScope(): bool;

    /**
     * Creates a new scope with and executes the given operation within. The scope
     * is automatically removed once the operation finishes or throws.
     *
     * @param callable $callback The callback to be executed
     */
    public function withScope(callable $callback): void;

    /**
     * Calls the given callback passing to it the current scope so that any
     * operation can be run within its context.
     *
     * @param callable $callback The callback to be executed
     */
    public function configureScope(callable $callback): void;

    /**
     * Binds the given client to the current scope.
     *
     * @param ClientInterface $client The client
     */
    public function bindClient(ClientInterface $client): void;

    /**
     * Captures a message event and sends it to Sentry.
     *
     * @param string   $message The message
     * @param Severity $level   The severity level of the message
     */
    public function captureMessage(string $message, ?Severity $level = null): ?string;

    /**
     * Captures an exception event and sends it to Sentry.
     *
     * @param \Throwable $exception The exception
     */
    public function captureException(\Throwable $exception): ?string;

    /**
     * Captures a new event using the provided data.
     *
     * @param array $payload The data of the event being captured
     */
    public function captureEvent(array $payload): ?string;

    /**
     * Captures an event that logs the last occurred error.
     */
    public function captureLastError(): ?string;

    /**
     * Records a new breadcrumb which will be attached to future events. They
     * will be added to subsequent events to provide more context on user's
     * actions prior to an error or crash.
     *
     * @param Breadcrumb $breadcrumb The breadcrumb to record
     *
     * @return bool Whether the breadcrumb was actually added to the current scope
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): bool;

    /**
     * Returns the current global Hub.
     *
     * @return HubInterface
     *
     * @deprecated since version 2.2, to be removed in 3.0
     * @see SentrySdk::getCurrentHub()
     */
    public static function getCurrent(): self;

    /**
     * Sets the Hub as the current.
     *
     * @param HubInterface $hub The Hub that will become the current one
     *
     * @return HubInterface
     *
     * @deprecated since version 2.2, to be removed in 3.0
     * @see SentrySdk::setCurrentHub()
     */
    public static function setCurrent(self $hub): self;

    /**
     * Gets the integration whose FQCN matches the given one if it's available on the current client.
     *
     * @param string $className The FQCN of the integration
     */
    public function getIntegration(string $className): ?IntegrationInterface;
}
