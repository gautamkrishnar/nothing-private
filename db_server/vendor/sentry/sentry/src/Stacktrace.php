<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Serializer\SerializerInterface;

/**
 * This class contains all the information about an error stacktrace.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class Stacktrace implements \JsonSerializable
{
    /**
     * @var Options The client options
     */
    protected $options;

    /**
     * @var SerializerInterface The serializer
     */
    protected $serializer;

    /**
     * @var RepresentationSerializerInterface The representation serializer
     */
    protected $representationSerializer;

    /**
     * @var Frame[] The frames that compose the stacktrace
     */
    protected $frames = [];

    /**
     * @var string[] The list of functions to import a file
     */
    protected static $importStatements = [
        'include',
        'include_once',
        'require',
        'require_once',
    ];

    /**
     * Stacktrace constructor.
     *
     * @param Options                           $options                  The client options
     * @param SerializerInterface               $serializer               The serializer
     * @param RepresentationSerializerInterface $representationSerializer The representation serializer
     */
    public function __construct(Options $options, SerializerInterface $serializer, RepresentationSerializerInterface $representationSerializer)
    {
        $this->options = $options;
        $this->serializer = $serializer;
        $this->representationSerializer = $representationSerializer;
    }

    /**
     * Creates a new instance of this class from the given backtrace.
     *
     * @param Options                           $options                  The client options
     * @param SerializerInterface               $serializer               The serializer
     * @param RepresentationSerializerInterface $representationSerializer The representation serializer
     * @param array                             $backtrace                The backtrace
     * @param string                            $file                     The file that originated the backtrace
     * @param int                               $line                     The line at which the backtrace originated
     *
     * @return static
     */
    public static function createFromBacktrace(Options $options, SerializerInterface $serializer, RepresentationSerializerInterface $representationSerializer, array $backtrace, string $file, int $line)
    {
        $stacktrace = new static($options, $serializer, $representationSerializer);

        foreach ($backtrace as $frame) {
            $stacktrace->addFrame($file, $line, $frame);

            $file = $frame['file'] ?? '[internal]';
            $line = $frame['line'] ?? 0;
        }

        // Add a final stackframe for the first method ever of this stacktrace
        $stacktrace->addFrame($file, $line, []);

        return $stacktrace;
    }

    /**
     * Gets the stacktrace frames.
     *
     * @return Frame[]
     */
    public function getFrames(): array
    {
        return $this->frames;
    }

    /**
     * Adds a new frame to the stacktrace.
     *
     * @param string $file           The file where the frame originated
     * @param int    $line           The line at which the frame originated
     * @param array  $backtraceFrame The data of the frame to add
     */
    public function addFrame(string $file, int $line, array $backtraceFrame): void
    {
        // The $file argument can be any of these formats:
        // </path/to/filename>
        // </path/to/filename>(<line number>) : eval()'d code
        // </path/to/filename>(<line number>) : runtime-created function
        if (preg_match('/^(.*)\((\d+)\) : (?:eval\(\)\'d code|runtime-created function)$/', $file, $matches)) {
            $file = $matches[1];
            $line = (int) $matches[2];
        }

        if (isset($backtraceFrame['class']) && isset($backtraceFrame['function'])) {
            $functionName = sprintf('%s::%s', $backtraceFrame['class'], $backtraceFrame['function']);
        } elseif (isset($backtraceFrame['function'])) {
            $functionName = $backtraceFrame['function'];
        } else {
            $functionName = null;
        }

        $frame = new Frame($functionName, $this->stripPrefixFromFilePath($file), $line);
        $sourceCodeExcerpt = $this->getSourceCodeExcerpt($file, $line, $this->options->getContextLines());

        if (isset($sourceCodeExcerpt['pre_context'])) {
            $frame->setPreContext($sourceCodeExcerpt['pre_context']);
        }

        if (isset($sourceCodeExcerpt['context_line'])) {
            $frame->setContextLine($sourceCodeExcerpt['context_line']);
        }

        if (isset($sourceCodeExcerpt['post_context'])) {
            $frame->setPostContext($sourceCodeExcerpt['post_context']);
        }

        // In case it's an Sentry internal frame, we mark it as in_app false
        if (null !== $functionName && 0 === strpos($functionName, 'Sentry\\')) {
            $frame->setIsInApp(false);
        }

        if (null !== $this->options->getProjectRoot()) {
            $excludedAppPaths = $this->options->getInAppExcludedPaths();
            $absoluteFilePath = @realpath($file) ?: $file;
            $isApplicationFile = 0 === strpos($absoluteFilePath, $this->options->getProjectRoot());

            if (!$isApplicationFile) {
                $frame->setIsInApp(false);
            } elseif (!empty($excludedAppPaths)) {
                foreach ($excludedAppPaths as $path) {
                    if (0 === mb_strpos($absoluteFilePath, $path)) {
                        $frame->setIsInApp(false);

                        break;
                    }
                }
            }
        }

        $frameArguments = $this->getFrameArguments($backtraceFrame);

        if (!empty($frameArguments)) {
            foreach ($frameArguments as $argumentName => $argumentValue) {
                $argumentValue = $this->representationSerializer->representationSerialize($argumentValue);

                if (is_numeric($argumentValue) || \is_string($argumentValue)) {
                    $frameArguments[(string) $argumentName] = mb_substr((string) $argumentValue, 0, $this->options->getMaxValueLength());
                } else {
                    $frameArguments[(string) $argumentName] = $argumentValue;
                }
            }

            $frame->setVars($frameArguments);
        }

        array_unshift($this->frames, $frame);
    }

    /**
     * Removes the frame at the given index from the stacktrace.
     *
     * @param int $index The index of the frame
     *
     * @throws \OutOfBoundsException If the index is out of range
     */
    public function removeFrame(int $index): void
    {
        if (!isset($this->frames[$index])) {
            throw new \OutOfBoundsException('Invalid frame index to remove.');
        }

        array_splice($this->frames, $index, 1);
    }

    /**
     * Gets the stacktrace frames (this is the same as calling the getFrames
     * method).
     *
     * @return Frame[]
     */
    public function toArray(): array
    {
        return $this->frames;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Gets an excerpt of the source code around a given line.
     *
     * @param string $path            The file path
     * @param int    $lineNumber      The line to centre about
     * @param int    $maxLinesToFetch The maximum number of lines to fetch
     */
    protected function getSourceCodeExcerpt(string $path, int $lineNumber, int $maxLinesToFetch): array
    {
        if (@!is_readable($path) || !is_file($path)) {
            return [];
        }

        $frame = [
            'pre_context' => [],
            'context_line' => '',
            'post_context' => [],
        ];

        $target = max(0, ($lineNumber - ($maxLinesToFetch + 1)));
        $currentLineNumber = $target + 1;

        try {
            $file = new \SplFileObject($path);
            $file->seek($target);

            while (!$file->eof()) {
                /** @var string $line */
                $line = $file->current();
                $line = rtrim($line, "\r\n");

                if ($currentLineNumber == $lineNumber) {
                    $frame['context_line'] = $line;
                } elseif ($currentLineNumber < $lineNumber) {
                    $frame['pre_context'][] = $line;
                } elseif ($currentLineNumber > $lineNumber) {
                    $frame['post_context'][] = $line;
                }

                ++$currentLineNumber;

                if ($currentLineNumber > $lineNumber + $maxLinesToFetch) {
                    break;
                }

                $file->next();
            }
        } catch (\Exception $exception) {
            // Do nothing, if any error occurs while trying to get the excerpts
            // it's not a drama
        }

        $frame['pre_context'] = $this->serializer->serialize($frame['pre_context']);
        $frame['context_line'] = $this->serializer->serialize($frame['context_line']);
        $frame['post_context'] = $this->serializer->serialize($frame['post_context']);

        return $frame;
    }

    /**
     * Removes from the given file path the specified prefixes.
     *
     * @param string $filePath The path to the file
     */
    protected function stripPrefixFromFilePath(string $filePath): string
    {
        foreach ($this->options->getPrefixes() as $prefix) {
            if (0 === mb_strpos($filePath, $prefix)) {
                return mb_substr($filePath, mb_strlen($prefix));
            }
        }

        return $filePath;
    }

    /**
     * Gets the values of the arguments of the given stackframe.
     *
     * @param array $frame The frame from where arguments are retrieved
     */
    protected function getFrameArgumentsValues(array $frame): array
    {
        if (empty($frame['args'])) {
            return [];
        }

        $result = [];

        if (\is_string(array_keys($frame['args'])[0])) {
            $result = array_map([$this, 'serializeArgument'], $frame['args']);
        } else {
            $index = 0;
            foreach (array_values($frame['args']) as $argument) {
                $result['param' . (++$index)] = $this->serializeArgument($argument);
            }
        }

        return $result;
    }

    /**
     * Gets the arguments of the given stackframe.
     *
     * @param array $frame The frame from where arguments are retrieved
     */
    public function getFrameArguments(array $frame): array
    {
        if (!isset($frame['args'])) {
            return [];
        }

        // The Reflection API seems more appropriate if we associate it with the frame
        // where the function is actually called (since we're treating them as function context)
        if (!isset($frame['function'])) {
            return $this->getFrameArgumentsValues($frame);
        }

        if (false !== strpos($frame['function'], '__lambda_func')) {
            return $this->getFrameArgumentsValues($frame);
        }

        if (false !== strpos($frame['function'], '{closure}')) {
            return $this->getFrameArgumentsValues($frame);
        }

        if (isset($frame['class']) && 'Closure' === $frame['class']) {
            return $this->getFrameArgumentsValues($frame);
        }

        if (\in_array($frame['function'], static::$importStatements, true)) {
            if (empty($frame['args'])) {
                return [];
            }

            return [
                'param1' => $this->serializeArgument($frame['args'][0]),
            ];
        }

        try {
            if (isset($frame['class'])) {
                if (method_exists($frame['class'], $frame['function'])) {
                    $reflection = new \ReflectionMethod($frame['class'], $frame['function']);
                } elseif ('::' === $frame['type']) {
                    $reflection = new \ReflectionMethod($frame['class'], '__callStatic');
                } else {
                    $reflection = new \ReflectionMethod($frame['class'], '__call');
                }
            } elseif (\function_exists($frame['function'])) {
                $reflection = new \ReflectionFunction($frame['function']);
            } else {
                return $this->getFrameArgumentsValues($frame);
            }
        } catch (\ReflectionException $ex) {
            return $this->getFrameArgumentsValues($frame);
        }

        $params = $reflection->getParameters();
        $args = [];

        foreach ($frame['args'] as $index => $arg) {
            $arg = $this->serializeArgument($arg);

            if (isset($params[$index])) {
                // Assign the argument by the parameter name
                $args[$params[$index]->name] = $arg;
            } else {
                $args['param' . $index] = $arg;
            }
        }

        return $args;
    }

    /**
     * Serializes the given argument.
     *
     * @param mixed $arg The argument to serialize
     *
     * @return mixed
     */
    protected function serializeArgument($arg)
    {
        $maxValueLength = $this->options->getMaxValueLength();

        if (\is_array($arg)) {
            $result = [];

            foreach ($arg as $key => $value) {
                if (\is_string($value) || is_numeric($value)) {
                    $result[$key] = mb_substr((string) $value, 0, $maxValueLength);
                } else {
                    $result[$key] = $value;
                }
            }

            return $result;
        } elseif (\is_string($arg) || is_numeric($arg)) {
            return mb_substr((string) $arg, 0, $maxValueLength);
        } else {
            return $arg;
        }
    }
}
