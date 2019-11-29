<?php

declare(strict_types=1);

namespace Sentry\State;

use Sentry\Breadcrumb;
use Sentry\ClientInterface;
use Sentry\Integration\IntegrationInterface;
use Sentry\SentrySdk;
use Sentry\Severity;

/**
 * This class is a basic implementation of the {@see HubInterface} interface.
 */
final class Hub implements HubInterface
{
    /**
     * @var Layer[] The stack of client/scope pairs
     */
    private $stack = [];

    /**
     * @var string|null The ID of the last captured event
     */
    private $lastEventId;

    /**
     * Hub constructor.
     *
     * @param ClientInterface|null $client The client bound to the hub
     * @param Scope|null           $scope  The scope bound to the hub
     */
    public function __construct(?ClientInterface $client = null, ?Scope $scope = null)
    {
        $this->stack[] = new Layer($client, $scope ?? new Scope());
    }

    /**
     * {@inheritdoc}
     */
    public function getClient(): ?ClientInterface
    {
        return $this->getStackTop()->getClient();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastEventId(): ?string
    {
        return $this->lastEventId;
    }

    /**
     * {@inheritdoc}
     */
    public function pushScope(): Scope
    {
        $clonedScope = clone $this->getScope();

        $this->stack[] = new Layer($this->getClient(), $clonedScope);

        return $clonedScope;
    }

    /**
     * {@inheritdoc}
     */
    public function popScope(): bool
    {
        if (1 === \count($this->stack)) {
            return false;
        }

        return null !== array_pop($this->stack);
    }

    /**
     * {@inheritdoc}
     */
    public function withScope(callable $callback): void
    {
        $scope = $this->pushScope();

        try {
            $callback($scope);
        } finally {
            $this->popScope();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureScope(callable $callback): void
    {
        $callback($this->getScope());
    }

    /**
     * {@inheritdoc}
     */
    public function bindClient(ClientInterface $client): void
    {
        $layer = $this->getStackTop();
        $layer->setClient($client);
    }

    /**
     * {@inheritdoc}
     */
    public function captureMessage(string $message, ?Severity $level = null): ?string
    {
        $client = $this->getClient();

        if (null !== $client) {
            return $this->lastEventId = $client->captureMessage($message, $level, $this->getScope());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureException(\Throwable $exception): ?string
    {
        $client = $this->getClient();

        if (null !== $client) {
            return $this->lastEventId = $client->captureException($exception, $this->getScope());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureEvent(array $payload): ?string
    {
        $client = $this->getClient();

        if (null !== $client) {
            return $this->lastEventId = $client->captureEvent($payload, $this->getScope());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function captureLastError(): ?string
    {
        $client = $this->getClient();

        if (null !== $client) {
            return $this->lastEventId = $client->captureLastError($this->getScope());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function addBreadcrumb(Breadcrumb $breadcrumb): bool
    {
        $client = $this->getClient();

        if (null === $client) {
            return false;
        }

        $options = $client->getOptions();
        $beforeBreadcrumbCallback = $options->getBeforeBreadcrumbCallback();
        $maxBreadcrumbs = $options->getMaxBreadcrumbs();

        if ($maxBreadcrumbs <= 0) {
            return false;
        }

        $breadcrumb = \call_user_func($beforeBreadcrumbCallback, $breadcrumb);

        if (null !== $breadcrumb) {
            $this->getScope()->addBreadcrumb($breadcrumb, $maxBreadcrumbs);
        }

        return null !== $breadcrumb;
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
        @trigger_error(sprintf('The %s() method is deprecated since version 2.2 and will be removed in 3.0. Use SentrySdk::setCurrentHub() instead.', __METHOD__), E_USER_DEPRECATED);

        SentrySdk::setCurrentHub($hub);

        return $hub;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegration(string $className): ?IntegrationInterface
    {
        $client = $this->getClient();

        if (null !== $client) {
            return $client->getIntegration($className);
        }

        return null;
    }

    /**
     * Gets the scope bound to the top of the stack.
     */
    private function getScope(): Scope
    {
        return $this->getStackTop()->getScope();
    }

    /**
     * Gets the topmost client/layer pair in the stack.
     */
    private function getStackTop(): Layer
    {
        return $this->stack[\count($this->stack) - 1];
    }
}
