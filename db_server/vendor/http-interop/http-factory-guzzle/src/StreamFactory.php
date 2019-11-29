<?php

namespace Http\Factory\Guzzle;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return \GuzzleHttp\Psr7\stream_for($content);
    }

    public function createStreamFromFile(string $file, string $mode = 'r'): StreamInterface
    {
        $resource = \GuzzleHttp\Psr7\try_fopen($file, $mode);

        return \GuzzleHttp\Psr7\stream_for($resource);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return \GuzzleHttp\Psr7\stream_for($resource);
    }
}
