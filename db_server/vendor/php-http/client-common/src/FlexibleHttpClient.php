<?php

declare(strict_types=1);

namespace Http\Client\Common;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Client\ClientInterface;

/**
 * A flexible http client, which implements both interface and will emulate
 * one contract, the other, or none at all depending on the injected client contract.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class FlexibleHttpClient implements HttpClient, HttpAsyncClient
{
    use HttpClientDecorator;
    use HttpAsyncClientDecorator;

    /**
     * @param ClientInterface|HttpAsyncClient $client
     */
    public function __construct($client)
    {
        if (!($client instanceof ClientInterface) && !($client instanceof HttpAsyncClient)) {
            throw new \LogicException(sprintf('Client must be an instance of %s or %s', ClientInterface::class, HttpAsyncClient::class));
        }

        $this->httpClient = $client;
        $this->httpAsyncClient = $client;

        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = new EmulatedHttpClient($this->httpClient);
        }

        if (!($this->httpAsyncClient instanceof HttpAsyncClient)) {
            $this->httpAsyncClient = new EmulatedHttpAsyncClient($this->httpAsyncClient);
        }
    }
}
