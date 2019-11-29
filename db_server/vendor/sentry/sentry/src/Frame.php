<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class represents a single frame of a stacktrace.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Frame implements \JsonSerializable
{
    /**
     * @var string|null The name of the function being called
     */
    private $functionName;

    /**
     * @var string The file where the frame originated
     */
    private $file;

    /**
     * @var int The line at which the frame originated
     */
    private $line;

    /**
     * @var string[] A list of source code lines before the one where the frame
     *               originated
     */
    private $preContext = [];

    /**
     * @var string|null The source code written at the line number of the file that
     *                  originated this frame
     */
    private $contextLine;

    /**
     * @var string[] A list of source code lines after the one where the frame
     *               originated
     */
    private $postContext = [];

    /**
     * @var bool Flag telling whether the frame is related to the execution of
     *           the relevant code in this stacktrace
     */
    private $inApp = true;

    /**
     * @var array A mapping of variables which were available within this
     *            frame (usually context-locals)
     */
    private $vars = [];

    /**
     * Initializes a new instance of this class using the provided information.
     *
     * @param string|null $functionName The name of the function being called
     * @param string      $file         The file where the frame originated
     * @param int         $line         The line at which the frame originated
     */
    public function __construct(?string $functionName, string $file, int $line)
    {
        $this->functionName = $functionName;
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * Gets the name of the function being called.
     */
    public function getFunctionName(): ?string
    {
        return $this->functionName;
    }

    /**
     * Gets the file where the frame originated.
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Gets the line at which the frame originated.
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Gets a list of source code lines before the one where the frame originated.
     *
     * @return string[]
     */
    public function getPreContext(): array
    {
        return $this->preContext;
    }

    /**
     * Sets a list of source code lines before the one where the frame originated.
     *
     * @param string[] $preContext The source code lines
     */
    public function setPreContext(array $preContext): void
    {
        $this->preContext = $preContext;
    }

    /**
     * Gets the source code written at the line number of the file that originated
     * this frame.
     */
    public function getContextLine(): ?string
    {
        return $this->contextLine;
    }

    /**
     * Sets the source code written at the line number of the file that originated
     * this frame.
     *
     * @param string|null $contextLine The source code line
     */
    public function setContextLine(?string $contextLine): void
    {
        $this->contextLine = $contextLine;
    }

    /**
     * Gets a list of source code lines after the one where the frame originated.
     *
     * @return string[]
     */
    public function getPostContext(): array
    {
        return $this->postContext;
    }

    /**
     * Sets a list of source code lines after the one where the frame originated.
     *
     * @param string[] $postContext The source code lines
     */
    public function setPostContext(array $postContext): void
    {
        $this->postContext = $postContext;
    }

    /**
     * Gets whether the frame is related to the execution of the relevant code
     * in this stacktrace.
     */
    public function isInApp(): bool
    {
        return $this->inApp;
    }

    /**
     * Sets whether the frame is related to the execution of the relevant code
     * in this stacktrace.
     *
     * @param bool $inApp flag indicating whether the frame is application-related
     */
    public function setIsInApp(bool $inApp): void
    {
        $this->inApp = $inApp;
    }

    /**
     * Gets a mapping of variables which were available within this frame
     * (usually context-locals).
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * Sets a mapping of variables which were available within this frame
     * (usually context-locals).
     *
     * @param array $vars The variables
     */
    public function setVars(array $vars): void
    {
        $this->vars = $vars;
    }

    /**
     * Returns an array representation of the data of this frame modeled according
     * to the specifications of the Sentry SDK Stacktrace Interface.
     */
    public function toArray(): array
    {
        $result = [
            'function' => $this->functionName,
            'filename' => $this->file,
            'lineno' => $this->line,
            'in_app' => $this->inApp,
        ];

        if (0 !== \count($this->preContext)) {
            $result['pre_context'] = $this->preContext;
        }

        if (null !== $this->contextLine) {
            $result['context_line'] = $this->contextLine;
        }

        if (0 !== \count($this->postContext)) {
            $result['post_context'] = $this->postContext;
        }

        if (!empty($this->vars)) {
            $result['vars'] = $this->vars;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
