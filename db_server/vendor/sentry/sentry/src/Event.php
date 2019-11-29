<?php

declare(strict_types=1);

namespace Sentry;

use Jean85\PrettyVersions;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sentry\Context\Context;
use Sentry\Context\RuntimeContext;
use Sentry\Context\ServerOsContext;
use Sentry\Context\TagsContext;
use Sentry\Context\UserContext;

/**
 * This is the base class for classes containing event data.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Event implements \JsonSerializable
{
    /**
     * @var UuidInterface The UUID
     */
    private $id;

    /**
     * @var string The date and time of when this event was generated
     */
    private $timestamp;

    /**
     * @var Severity The severity of this event
     */
    private $level;

    /**
     * @var string|null The name of the logger which created the record
     */
    private $logger;

    /**
     * @var string|null the name of the transaction (or culprit) which caused this exception
     */
    private $transaction;

    /**
     * @var string|null The name of the server (e.g. the host name)
     */
    private $serverName;

    /**
     * @var string|null The release of the program
     */
    private $release;

    /**
     * @var string|null The error message
     */
    private $message;

    /**
     * @var string|null The formatted error message
     */
    private $messageFormatted;

    /**
     * @var mixed[] The parameters to use to format the message
     */
    private $messageParams = [];

    /**
     * @var string|null The environment where this event generated (e.g. production)
     */
    private $environment;

    /**
     * @var array<string, string> A list of relevant modules and their versions
     */
    private $modules = [];

    /**
     * @var array<string, mixed> The request data
     */
    private $request = [];

    /**
     * @var ServerOsContext The server OS context data
     */
    private $serverOsContext;

    /**
     * @var RuntimeContext The runtime context data
     */
    private $runtimeContext;

    /**
     * @var UserContext The user context data
     */
    private $userContext;

    /**
     * @var Context An arbitrary mapping of additional metadata
     */
    private $extraContext;

    /**
     * @var TagsContext A List of tags associated to this event
     */
    private $tagsContext;

    /**
     * @var string[] An array of strings used to dictate the deduplication of this event
     */
    private $fingerprint = [];

    /**
     * @var Breadcrumb[] The associated breadcrumbs
     */
    private $breadcrumbs = [];

    /**
     * @var array<int, array<string, mixed>> The exceptions
     *
     * @psalm-var array<int, array{
     *     type: class-string,
     *     value: string,
     *     stacktrace: Stacktrace
     * }>
     */
    private $exceptions = [];

    /**
     * @var Stacktrace|null The stacktrace that generated this event
     */
    private $stacktrace;

    /**
     * @var string The Sentry SDK identifier
     */
    private $sdkIdentifier = Client::SDK_IDENTIFIER;

    /**
     * @var string The Sentry SDK version
     */
    private $sdkVersion;

    /**
     * Event constructor.
     *
     * @throws UnsatisfiedDependencyException if `Moontoast\Math\BigNumber` is not present
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $this->level = Severity::error();
        $this->serverOsContext = new ServerOsContext();
        $this->runtimeContext = new RuntimeContext();
        $this->userContext = new UserContext();
        $this->extraContext = new Context();
        $this->tagsContext = new TagsContext();
        $this->sdkVersion = PrettyVersions::getVersion('sentry/sentry')->getPrettyVersion();
    }

    /**
     * Gets the UUID of this event.
     */
    public function getId(): string
    {
        return str_replace('-', '', $this->id->toString());
    }

    /**
     * Gets the identifier of the SDK package that generated this event.
     *
     * @internal
     */
    public function getSdkIdentifier(): string
    {
        return $this->sdkIdentifier;
    }

    /**
     * Sets the identifier of the SDK package that generated this event.
     *
     * @internal
     */
    public function setSdkIdentifier(string $sdkIdentifier): void
    {
        $this->sdkIdentifier = $sdkIdentifier;
    }

    /**
     * Gets the version of the SDK package that generated this Event.
     *
     * @internal
     */
    public function getSdkVersion(): string
    {
        return $this->sdkVersion;
    }

    /**
     * Sets the version of the SDK package that generated this Event.
     *
     * @internal
     */
    public function setSdkVersion(string $sdkVersion): void
    {
        $this->sdkVersion = $sdkVersion;
    }

    /**
     * Gets the timestamp of when this event was generated.
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Gets the severity of this event.
     */
    public function getLevel(): Severity
    {
        return $this->level;
    }

    /**
     * Sets the severity of this event.
     *
     * @param Severity $level The severity
     */
    public function setLevel(Severity $level): void
    {
        $this->level = $level;
    }

    /**
     * Gets the name of the logger which created the event.
     */
    public function getLogger(): ?string
    {
        return $this->logger;
    }

    /**
     * Sets the name of the logger which created the event.
     *
     * @param string|null $logger The logger name
     */
    public function setLogger(?string $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Gets the name of the transaction (or culprit) which caused this
     * exception.
     */
    public function getTransaction(): ?string
    {
        return $this->transaction;
    }

    /**
     * Sets the name of the transaction (or culprit) which caused this
     * exception.
     *
     * @param string|null $transaction The transaction name
     */
    public function setTransaction(?string $transaction): void
    {
        $this->transaction = $transaction;
    }

    /**
     * Gets the name of the server.
     */
    public function getServerName(): ?string
    {
        return $this->serverName;
    }

    /**
     * Sets the name of the server.
     *
     * @param string|null $serverName The server name
     */
    public function setServerName(?string $serverName): void
    {
        $this->serverName = $serverName;
    }

    /**
     * Gets the release of the program.
     */
    public function getRelease(): ?string
    {
        return $this->release;
    }

    /**
     * Sets the release of the program.
     *
     * @param string|null $release The release
     */
    public function setRelease(?string $release): void
    {
        $this->release = $release;
    }

    /**
     * Gets the error message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Gets the formatted message.
     */
    public function getMessageFormatted(): ?string
    {
        return $this->messageFormatted;
    }

    /**
     * Gets the parameters to use to format the message.
     *
     * @return string[]
     */
    public function getMessageParams(): array
    {
        return $this->messageParams;
    }

    /**
     * Sets the error message.
     *
     * @param string      $message   The message
     * @param mixed[]     $params    The parameters to use to format the message
     * @param string|null $formatted The formatted message
     */
    public function setMessage(string $message, array $params = [], ?string $formatted = null): void
    {
        $this->message = $message;
        $this->messageParams = $params;
        $this->messageFormatted = $formatted;
    }

    /**
     * Gets a list of relevant modules and their versions.
     *
     * @return array<string, string>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Sets a list of relevant modules and their versions.
     *
     * @param array<string, string> $modules
     */
    public function setModules(array $modules): void
    {
        $this->modules = $modules;
    }

    /**
     * Gets the request data.
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * Sets the request data.
     *
     * @param array<string, mixed> $request The request data
     */
    public function setRequest(array $request): void
    {
        $this->request = $request;
    }

    /**
     * Gets an arbitrary mapping of additional metadata.
     */
    public function getExtraContext(): Context
    {
        return $this->extraContext;
    }

    /**
     * Gets a list of tags.
     */
    public function getTagsContext(): TagsContext
    {
        return $this->tagsContext;
    }

    /**
     * Gets the user context.
     */
    public function getUserContext(): UserContext
    {
        return $this->userContext;
    }

    /**
     * Gets the server OS context.
     */
    public function getServerOsContext(): ServerOsContext
    {
        return $this->serverOsContext;
    }

    /**
     * Gets the runtime context data.
     */
    public function getRuntimeContext(): RuntimeContext
    {
        return $this->runtimeContext;
    }

    /**
     * Gets an array of strings used to dictate the deduplication of this
     * event.
     *
     * @return string[]
     */
    public function getFingerprint(): array
    {
        return $this->fingerprint;
    }

    /**
     * Sets an array of strings used to dictate the deduplication of this
     * event.
     *
     * @param string[] $fingerprint The strings
     */
    public function setFingerprint(array $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * Gets the environment in which this event was generated.
     */
    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    /**
     * Sets the environment in which this event was generated.
     *
     * @param string|null $environment The name of the environment
     */
    public function setEnvironment(?string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * Gets the breadcrumbs.
     *
     * @return Breadcrumb[]
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Set new breadcrumbs to the event.
     *
     * @param Breadcrumb[] $breadcrumbs The breadcrumb array
     */
    public function setBreadcrumb(array $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Gets the exception.
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * Sets the exception.
     *
     * @param array<int, array<string, mixed>> $exceptions The exception
     *
     * @psalm-param array<int, array{
     *     type: class-string,
     *     value: string,
     *     stacktrace: Stacktrace
     * }> $exceptions
     */
    public function setExceptions(array $exceptions): void
    {
        $this->exceptions = $exceptions;
    }

    /**
     * Gets the stacktrace that generated this event.
     */
    public function getStacktrace(): ?Stacktrace
    {
        return $this->stacktrace;
    }

    /**
     * Sets the stacktrace that generated this event.
     *
     * @param Stacktrace $stacktrace The stacktrace instance
     */
    public function setStacktrace(Stacktrace $stacktrace): void
    {
        $this->stacktrace = $stacktrace;
    }

    /**
     * Gets the event as an array.
     *
     * @psalm-return array{
     *     event_id: string,
     *     timestamp: string,
     *     level: string,
     *     platform: string,
     *     sdk: array{
     *         name: string,
     *         version: string
     *     },
     *     logger?: string,
     *     transaction?: string,
     *     server_name?: string,
     *     release?: string,
     *     environment?: string,
     *     fingerprint?: string[],
     *     modules?: array<string, string>,
     *     extra?: mixed[],
     *     tags?: mixed[],
     *     user?: mixed[],
     *     contexts?: array{
     *         os?: mixed[],
     *         runtime?: mixed[]
     *     },
     *     breadcrumbs?: array{
     *         values: Breadcrumb[]
     *     },
     *     exception?: array{
     *         values: array{
     *             type: class-string,
     *             value: string,
     *             stacktrace?: array{
     *                 frames: Frame[]
     *             }
     *         }[]
     *     },
     *     request?: array<string, mixed>,
     *     message?: string|array{
     *         message: string,
     *         params: mixed[],
     *         formatted: string
     *     }
     * }
     */
    public function toArray(): array
    {
        $data = [
            'event_id' => str_replace('-', '', $this->id->toString()),
            'timestamp' => $this->timestamp,
            'level' => (string) $this->level,
            'platform' => 'php',
            'sdk' => [
                'name' => $this->sdkIdentifier,
                'version' => $this->getSdkVersion(),
            ],
        ];

        if (null !== $this->logger) {
            $data['logger'] = $this->logger;
        }

        if (null !== $this->transaction) {
            $data['transaction'] = $this->transaction;
        }

        if (null !== $this->serverName) {
            $data['server_name'] = $this->serverName;
        }

        if (null !== $this->release) {
            $data['release'] = $this->release;
        }

        if (null !== $this->environment) {
            $data['environment'] = $this->environment;
        }

        if (!empty($this->fingerprint)) {
            $data['fingerprint'] = $this->fingerprint;
        }

        if (!empty($this->modules)) {
            $data['modules'] = $this->modules;
        }

        if (!$this->extraContext->isEmpty()) {
            $data['extra'] = $this->extraContext->toArray();
        }

        if (!$this->tagsContext->isEmpty()) {
            $data['tags'] = $this->tagsContext->toArray();
        }

        if (!$this->userContext->isEmpty()) {
            $data['user'] = $this->userContext->toArray();
        }

        if (!$this->serverOsContext->isEmpty()) {
            $data['contexts']['os'] = $this->serverOsContext->toArray();
        }

        if (!$this->runtimeContext->isEmpty()) {
            $data['contexts']['runtime'] = $this->runtimeContext->toArray();
        }

        if (!empty($this->breadcrumbs)) {
            $data['breadcrumbs']['values'] = $this->breadcrumbs;
        }

        foreach (array_reverse($this->exceptions) as $exception) {
            $exceptionData = [
                'type' => $exception['type'],
                'value' => $exception['value'],
            ];

            if (isset($exception['stacktrace'])) {
                $exceptionData['stacktrace'] = [
                    'frames' => $exception['stacktrace']->toArray(),
                ];
            }

            $data['exception']['values'][] = $exceptionData;
        }

        if (null !== $this->stacktrace) {
            $data['stacktrace'] = [
                'frames' => $this->stacktrace->toArray(),
            ];
        }

        if (!empty($this->request)) {
            $data['request'] = $this->request;
        }

        if (null !== $this->message) {
            if (empty($this->messageParams)) {
                $data['message'] = $this->message;
            } else {
                $data['message'] = [
                    'message' => $this->message,
                    'params' => $this->messageParams,
                    'formatted' => $this->messageFormatted ?? vsprintf($this->message, $this->messageParams),
                ];
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
