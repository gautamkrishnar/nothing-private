<?php

declare(strict_types=1);

namespace Sentry;

use Http\Client\Common\Plugin as PluginInterface;
use Http\Client\HttpAsyncClient;
use Http\Message\MessageFactory as MessageFactoryInterface;
use Http\Message\UriFactory as UriFactoryInterface;
use Sentry\Serializer\RepresentationSerializerInterface;
use Sentry\Serializer\SerializerInterface;
use Sentry\Transport\TransportInterface;

/**
 * A configurable builder for Client objects.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
interface ClientBuilderInterface
{
    /**
     * Creates a new instance of this builder.
     *
     * @param array $options The client options, in naked array form
     *
     * @return static
     */
    public static function create(array $options = []): self;

    /**
     * The options that will be used to create the {@see Client}.
     */
    public function getOptions(): Options;

    /**
     * Sets the factory to use to create URIs.
     *
     * @param UriFactoryInterface $uriFactory The factory
     *
     * @return $this
     */
    public function setUriFactory(UriFactoryInterface $uriFactory): self;

    /**
     * Sets the factory to use to create PSR-7 messages.
     *
     * @param MessageFactoryInterface $messageFactory The factory
     *
     * @return $this
     */
    public function setMessageFactory(MessageFactoryInterface $messageFactory): self;

    /**
     * Sets the transport that will be used to send events.
     *
     * @param TransportInterface $transport The transport
     *
     * @return $this
     */
    public function setTransport(TransportInterface $transport): self;

    /**
     * Sets the HTTP client.
     *
     * @param HttpAsyncClient $httpClient The HTTP client
     *
     * @return $this
     */
    public function setHttpClient(HttpAsyncClient $httpClient): self;

    /**
     * Adds a new HTTP client plugin to the end of the plugins chain.
     *
     * @param PluginInterface $plugin The plugin instance
     *
     * @return $this
     */
    public function addHttpClientPlugin(PluginInterface $plugin): self;

    /**
     * Removes a HTTP client plugin by its fully qualified class name (FQCN).
     *
     * @param string $className The class name
     *
     * @return $this
     */
    public function removeHttpClientPlugin(string $className): self;

    /**
     * Gets the instance of the client built using the configured options.
     */
    public function getClient(): ClientInterface;

    /**
     * Sets a serializer instance to be injected as a dependency of the client.
     *
     * @param SerializerInterface $serializer The serializer to be used by the client to fill the events
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer): self;

    /**
     * Sets a representation serializer instance to be injected as a dependency of the client.
     *
     * @param RepresentationSerializerInterface $representationSerializer The representation serializer, used to serialize function
     *                                                                    arguments in stack traces, to have string representation
     *                                                                    of non-string values
     *
     * @return $this
     */
    public function setRepresentationSerializer(RepresentationSerializerInterface $representationSerializer): self;

    /**
     * Sets the SDK identifier to be passed onto {@see Event} and HTTP User-Agent header.
     *
     * @param string $sdkIdentifier The SDK identifier to be sent in {@see Event} and HTTP User-Agent headers
     *
     * @return $this
     *
     * @internal
     */
    public function setSdkIdentifier(string $sdkIdentifier): self;

    /**
     * Sets the SDK version to be passed onto {@see Event} and HTTP User-Agent header.
     *
     * @param string $sdkVersion The version of the SDK in use, to be sent alongside the SDK identifier
     *
     * @return $this
     *
     * @internal
     */
    public function setSdkVersion(string $sdkVersion): self;
}
