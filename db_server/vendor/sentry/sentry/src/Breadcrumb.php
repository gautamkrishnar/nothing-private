<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Exception\InvalidArgumentException;

/**
 * This class stores all the informations about a breadcrumb.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Breadcrumb implements \JsonSerializable
{
    /**
     * This constant defines the default breadcrumb type.
     */
    public const TYPE_DEFAULT = 'default';

    /**
     * This constant defines the http breadcrumb type.
     */
    public const TYPE_HTTP = 'http';

    /**
     * This constant defines the user breadcrumb type.
     */
    public const TYPE_USER = 'user';

    /**
     * This constant defines the navigation breadcrumb type.
     */
    public const TYPE_NAVIGATION = 'navigation';

    /**
     * This constant defines the error breadcrumb type.
     */
    public const TYPE_ERROR = 'error';

    /**
     * This constant defines the debug level for a breadcrumb.
     */
    public const LEVEL_DEBUG = 'debug';

    /**
     * This constant defines the info level for a breadcrumb.
     */
    public const LEVEL_INFO = 'info';

    /**
     * This constant defines the warning level for a breadcrumb.
     */
    public const LEVEL_WARNING = 'warning';

    /**
     * This constant defines the error level for a breadcrumb.
     */
    public const LEVEL_ERROR = 'error';

    /**
     * This constant defines the critical level for a breadcrumb.
     *
     * @deprecated since version 2.2.2, to be removed in 3.0; use fatal instead.
     */
    public const LEVEL_CRITICAL = 'critical';

    /**
     * This constant defines the fatal level for a breadcrumb.
     */
    public const LEVEL_FATAL = 'fatal';

    /**
     * This constant defines the list of values allowed to be set as severity
     * level of the breadcrumb.
     */
    private const ALLOWED_LEVELS = [
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
        self::LEVEL_CRITICAL,
        self::LEVEL_FATAL,
    ];

    /**
     * @var string The category of the breadcrumb
     */
    private $category;

    /**
     * @var string The type of breadcrumb
     */
    private $type;

    /**
     * @var string|null The message of the breadcrumb
     */
    private $message;

    /**
     * @var string The level of the breadcrumb
     */
    private $level;

    /**
     * @var array The meta data of the breadcrumb
     */
    private $metadata;

    /**
     * @var float The timestamp of the breadcrumb
     */
    private $timestamp;

    /**
     * Constructor.
     *
     * @param string      $level    The error level of the breadcrumb
     * @param string      $type     The type of the breadcrumb
     * @param string      $category The category of the breadcrumb
     * @param string|null $message  Optional text message
     * @param array       $metadata Additional information about the breadcrumb
     */
    public function __construct(string $level, string $type, string $category, ?string $message = null, array $metadata = [])
    {
        if (!\in_array($level, self::ALLOWED_LEVELS, true)) {
            throw new InvalidArgumentException('The value of the $level argument must be one of the Breadcrumb::LEVEL_* constants.');
        }

        $this->type = $type;
        $this->level = $level;
        $this->category = $category;
        $this->message = $message;
        $this->metadata = $metadata;
        $this->timestamp = microtime(true);
    }

    /**
     * Maps the severity of the error to one of the levels supported by the
     * breadcrumbs.
     *
     * @param \ErrorException $exception The exception
     */
    public static function levelFromErrorException(\ErrorException $exception): string
    {
        switch ($exception->getSeverity()) {
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
            case E_WARNING:
            case E_USER_WARNING:
            case E_RECOVERABLE_ERROR:
                return self::LEVEL_WARNING;
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
                return self::LEVEL_FATAL;
            case E_USER_ERROR:
                return self::LEVEL_ERROR;
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
                return self::LEVEL_INFO;
            default:
                return self::LEVEL_ERROR;
        }
    }

    /**
     * Gets the breadcrumb type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type of the breadcrumb.
     *
     * @param string $type The type
     *
     * @return static
     */
    public function withType(string $type): self
    {
        if ($type === $this->type) {
            return $this;
        }

        $new = clone $this;
        $new->type = $type;

        return $new;
    }

    /**
     * Gets the breadcrumb level.
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Sets the error level of the breadcrumb.
     *
     * @param string $level The level
     *
     * @return static
     */
    public function withLevel(string $level): self
    {
        if (!\in_array($level, self::ALLOWED_LEVELS, true)) {
            throw new InvalidArgumentException('The value of the $level argument must be one of the Breadcrumb::LEVEL_* constants.');
        }

        if ($level === $this->level) {
            return $this;
        }

        $new = clone $this;
        $new->level = $level;

        return $new;
    }

    /**
     * Gets the breadcrumb category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Sets the breadcrumb category.
     *
     * @param string $category The category
     *
     * @return static
     */
    public function withCategory(string $category): self
    {
        if ($category === $this->category) {
            return $this;
        }

        $new = clone $this;
        $new->category = $category;

        return $new;
    }

    /**
     * Gets the breadcrumb message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Sets the breadcrumb message.
     *
     * @param string $message The message
     *
     * @return static
     */
    public function withMessage(string $message): self
    {
        if ($message === $this->message) {
            return $this;
        }

        $new = clone $this;
        $new->message = $message;

        return $new;
    }

    /**
     * Gets the breadcrumb meta data.
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Returns an instance of this class with the provided metadata, replacing
     * any existing values of any metadata with the same name.
     *
     * @param string $name  The name of the metadata
     * @param mixed  $value The value
     *
     * @return static
     */
    public function withMetadata(string $name, $value): self
    {
        if (isset($this->metadata[$name]) && $value === $this->metadata[$name]) {
            return $this;
        }

        $new = clone $this;
        $new->metadata[$name] = $value;

        return $new;
    }

    /**
     * Returns an instance of this class without the specified metadata
     * information.
     *
     * @param string $name The name of the metadata
     *
     * @return static|Breadcrumb
     */
    public function withoutMetadata(string $name): self
    {
        if (!isset($this->metadata[$name])) {
            return $this;
        }

        $new = clone $this;

        unset($new->metadata[$name]);

        return $new;
    }

    /**
     * Gets the breadcrumb timestamp.
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    /**
     * Gets the breadcrumb as an array.
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'category' => $this->category,
            'level' => $this->level,
            'message' => $this->message,
            'timestamp' => $this->timestamp,
            'data' => $this->metadata,
        ];
    }

    /**
     * Helper method to create an instance of this class from an array of data.
     *
     * @param array $data Data used to populate the breadcrumb
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['level'],
            $data['type'] ?? self::TYPE_DEFAULT,
            $data['category'],
            $data['message'] ?? null,
            $data['data'] ?? []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
