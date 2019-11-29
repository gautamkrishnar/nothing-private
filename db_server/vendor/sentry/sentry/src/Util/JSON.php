<?php

declare(strict_types=1);

namespace Sentry\Util;

use Sentry\Exception\JsonException;

/**
 * This class provides some utility methods to encode/decode JSON data.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class JSON
{
    /**
     * Encodes the given data into JSON.
     *
     * @param mixed $data The data to encode
     *
     * @throws JsonException If the encoding failed
     */
    public static function encode($data): string
    {
        $encodedData = json_encode($data, JSON_UNESCAPED_UNICODE);

        if (JSON_ERROR_NONE !== json_last_error() || false === $encodedData) {
            throw new JsonException(sprintf('Could not encode value into JSON format. Error was: "%s".', json_last_error_msg()));
        }

        return $encodedData;
    }

    /**
     * Decodes the given data from JSON.
     *
     * @param string $data The data to decode
     *
     * @return mixed
     *
     * @throws JsonException If the decoding failed
     */
    public static function decode(string $data)
    {
        $decodedData = json_decode($data, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new JsonException(sprintf('Could not decode value from JSON format. Error was: "%s".', json_last_error_msg()));
        }

        return $decodedData;
    }
}
