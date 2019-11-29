<?php

declare(strict_types=1);

namespace Sentry;

use Sentry\Exception\EventCreationException;
use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Serializer\SerializerInterface;

/**
 * Factory for the {@see Event} class.
 */
final class EventFactory implements EventFactoryInterface
{
    /**
     * @var SerializerInterface The serializer
     */
    private $serializer;

    /**
     * @var RepresentationSerializerInterface The representation serializer
     */
    private $representationSerializer;

    /**
     * @var Options The Sentry options
     */
    private $options;

    /**
     * @var string The Sentry SDK identifier
     */
    private $sdkIdentifier;

    /**
     * @var string the SDK version of the Client
     */
    private $sdkVersion;

    /**
     * EventFactory constructor.
     *
     * @param SerializerInterface               $serializer               The serializer
     * @param RepresentationSerializerInterface $representationSerializer The serializer for function arguments
     * @param Options                           $options                  The SDK configuration options
     * @param string                            $sdkIdentifier            The Sentry SDK identifier
     * @param string                            $sdkVersion               The Sentry SDK version
     */
    public function __construct(SerializerInterface $serializer, RepresentationSerializerInterface $representationSerializer, Options $options, string $sdkIdentifier, string $sdkVersion)
    {
        $this->serializer = $serializer;
        $this->representationSerializer = $representationSerializer;
        $this->options = $options;
        $this->sdkIdentifier = $sdkIdentifier;
        $this->sdkVersion = $sdkVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithStacktrace(array $payload): Event
    {
        $event = $this->create($payload);

        if (!$event->getStacktrace()) {
            $stacktrace = Stacktrace::createFromBacktrace($this->options, $this->serializer, $this->representationSerializer, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), __FILE__, __LINE__);

            $event->setStacktrace($stacktrace);
        }

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload): Event
    {
        try {
            $event = new Event();
        } catch (\Throwable $exception) {
            throw new EventCreationException($exception);
        }

        $event->setSdkIdentifier($this->sdkIdentifier);
        $event->setSdkVersion($this->sdkVersion);
        $event->setServerName($this->options->getServerName());
        $event->setRelease($this->options->getRelease());
        $event->getTagsContext()->merge($this->options->getTags());
        $event->setEnvironment($this->options->getEnvironment());

        if (isset($payload['logger'])) {
            $event->setLogger($payload['logger']);
        }

        $message = isset($payload['message']) ? mb_substr($payload['message'], 0, $this->options->getMaxValueLength()) : null;
        $messageParams = $payload['message_params'] ?? [];
        $messageFormatted = isset($payload['message_formatted']) ? mb_substr($payload['message_formatted'], 0, $this->options->getMaxValueLength()) : null;

        if (null !== $message) {
            $event->setMessage($message, $messageParams, $messageFormatted);
        }

        if (isset($payload['exception']) && $payload['exception'] instanceof \Throwable) {
            $this->addThrowableToEvent($event, $payload['exception']);
        }

        if (isset($payload['level']) && $payload['level'] instanceof Severity) {
            $event->setLevel($payload['level']);
        }

        if (isset($payload['stacktrace']) && $payload['stacktrace'] instanceof Stacktrace) {
            $event->setStacktrace($payload['stacktrace']);
        }

        return $event;
    }

    /**
     * Stores the given exception in the passed event.
     *
     * @param Event      $event     The event that will be enriched with the exception
     * @param \Throwable $exception The exception that will be processed and added to the event
     */
    private function addThrowableToEvent(Event $event, \Throwable $exception): void
    {
        if ($exception instanceof \ErrorException) {
            $event->setLevel(Severity::fromError($exception->getSeverity()));
        }

        $exceptions = [];
        $currentException = $exception;

        do {
            $exceptions[] = [
                'type' => \get_class($currentException),
                'value' => $currentException->getMessage(),
                'stacktrace' => Stacktrace::createFromBacktrace(
                    $this->options,
                    $this->serializer,
                    $this->representationSerializer,
                    $currentException->getTrace(),
                    $currentException->getFile(),
                    $currentException->getLine()
                ),
            ];
        } while ($currentException = $currentException->getPrevious());

        $event->setExceptions($exceptions);
    }
}
