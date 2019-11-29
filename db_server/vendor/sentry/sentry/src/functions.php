<?php

declare(strict_types=1);

namespace Sentry;

/**
 * Creates a new Client and Hub which will be set as current.
 *
 * @param array $options The client options
 */
function init(array $options = []): void
{
    $client = ClientBuilder::create($options)->getClient();

    SentrySdk::init()->bindClient($client);
}

/**
 * Captures a message event and sends it to Sentry.
 *
 * @param string   $message The message
 * @param Severity $level   The severity level of the message
 */
function captureMessage(string $message, ?Severity $level = null): ?string
{
    return SentrySdk::getCurrentHub()->captureMessage($message, $level);
}

/**
 * Captures an exception event and sends it to Sentry.
 *
 * @param \Throwable $exception The exception
 */
function captureException(\Throwable $exception): ?string
{
    return SentrySdk::getCurrentHub()->captureException($exception);
}

/**
 * Captures a new event using the provided data.
 *
 * @param array $payload The data of the event being captured
 */
function captureEvent(array $payload): ?string
{
    return SentrySdk::getCurrentHub()->captureEvent($payload);
}

/**
 * Logs the most recent error (obtained with {@link error_get_last}).
 */
function captureLastError(): ?string
{
    return SentrySdk::getCurrentHub()->captureLastError();
}

/**
 * Records a new breadcrumb which will be attached to future events. They
 * will be added to subsequent events to provide more context on user's
 * actions prior to an error or crash.
 *
 * @param Breadcrumb $breadcrumb The breadcrumb to record
 */
function addBreadcrumb(Breadcrumb $breadcrumb): void
{
    SentrySdk::getCurrentHub()->addBreadcrumb($breadcrumb);
}

/**
 * Calls the given callback passing to it the current scope so that any
 * operation can be run within its context.
 *
 * @param callable $callback The callback to be executed
 */
function configureScope(callable $callback): void
{
    SentrySdk::getCurrentHub()->configureScope($callback);
}

/**
 * Creates a new scope with and executes the given operation within. The scope
 * is automatically removed once the operation finishes or throws.
 *
 * @param callable $callback The callback to be executed
 */
function withScope(callable $callback): void
{
    SentrySdk::getCurrentHub()->withScope($callback);
}
