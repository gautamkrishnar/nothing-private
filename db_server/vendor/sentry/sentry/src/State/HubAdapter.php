<?php

declare(strict_types=1);

namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\Integration\IntegrationInterface;
use Sentry\SentrySdk;
use Sentry\Severity;

/**
 * An implementation of {@see HubInterface} that uses {@see SentrySdk} internally
 * to manage the current hub.
 */
final class HubAdapter implements HubInterface
{
    /**
     * @var self The single instance which forwards all calls to {@see SentrySdk}
     */
    private static $instance;

    /**
     * Constructor.
     */
    private function __construct()
    {
    }

    /**
     * Gets the instance of this class. This is a singleton, so once initialized
     * you will always get the same instance.
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getClient(): ?ClientInterface
    {
        return SentrySdk::getCurrentHub()->getClient();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastEventId(): ?string
    {
        return SentrySdk::getCurrentHub()->getLastEventId();
    }

    /**
     * {@inheritdoc}
     */
    public function pushScope(): Scope
    {
        return SentrySdk::getCurrentHub()->pushScope();
    }

    /**
     * {@inheritdoc}
     */
    public function popScope(): bool
    {
        return SentrySdk::getCurrentHub()->popScope();
    }

    /**
     * {@inheritdoc}
     */
    public function withScope(callable $callback): void
    {
        SentrySdk::getCurrentHub()->withScope($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function configureScope(callable $callback): void
    {
        SentrySdk::getCurrentHub()->configureScope($callback);
    }

    /**
     * {@inheritdoc}
     */
    public function bindClient(ClientInterface $client): void
    {
        SentrySdk::getCurrentHub()->bindClient($client);
    }

    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?Severity $level = null): ?string
    {
        return SentrySdk::getCurrentHub()->captureMessage($message, $level);
    }

    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception): ?string
    {
        return SentrySdk::getCurrentHub()->captureException($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function captureEvent(array $payload): ?string
    {
        return SentrySdk::getCurrentHub()->captureEvent($payload);
    }

    /**
     * {@inheritdoc}
     */
    public function captureLastError(): ?string
    {
        return SentrySdk::getCurrentHub()->captureLastError();
    }

    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): bool
    {
        return SentrySdk::getCurrentHub()->addBreadcrumb($breadcrumb);
    }

    /**
     * {@inheritdoc}
     */
    public static function getCurrent(): HubInterface
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 2.2 and will be removed in 3.0. Use SentrySdk::getCurrentHub() instead.', __METHOD__), E_USER_DEPRECATED);

        return SentrySdk::getCurrentHub();
    }

    /**
     * {@inheritdoc}
     */
    public static function setCurrent(HubInterface $hub): HubInterface
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 2.2 and will be removed in 3.0. Use SentrySdk::getCurrentHub() instead.', __METHOD__), E_USER_DEPRECATED);

        return SentrySdk::setCurrentHub($hub);
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegration(string $className): ?IntegrationInterface
    {
        return SentrySdk::getCurrentHub()->getIntegration($className);
    }

    /**
     * @see https://www.php.net/manual/en/language.oop5.cloning.php#object.clone
     */
    public function __clone()
    {
        throw new \BadMethodCallException('Cloning is forbidden.');
    }

    /**
     * @see https://www.php.net/manual/en/language.oop5.magic.php#object.wakeup
     */
    public function __wakeup()
    {
        throw new \BadMethodCallException('Unserializing instances of this class is forbidden.');
    }
}
