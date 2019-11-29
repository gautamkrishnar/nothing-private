<?php

declare(strict_types=1);

namespace Sentry\Context;

/**
 * This class is a specialized version of the base `Context` adapted to work
 * for the tags context.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class TagsContext extends Context
{
    /**
     * {@inheritdoc}
     */
    public function merge(array $data, bool $recursive = false): void
    {
        if ($recursive) {
            throw new \InvalidArgumentException('The tags context does not allow recursive merging of its data.');
        }

        parent::merge(self::sanitizeData($data));
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        parent::setData(self::sanitizeData($data));
    }

    /**
     * {@inheritdoc}
     */
    public function replaceData(array $data): void
    {
        parent::replaceData(self::sanitizeData($data));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        if (is_numeric($value)) {
            $value = (string) $value;
        }

        if (!\is_string($value)) {
            throw new \InvalidArgumentException('The $value argument must be a string.');
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Sanitizes the given data by converting numeric values to strings.
     *
     * @param array $data The data to sanitize
     *
     * @throws \InvalidArgumentException If any of the values of the input data
     *                                   is not a number or a string
     */
    private static function sanitizeData(array $data): array
    {
        foreach ($data as &$value) {
            if (is_numeric($value)) {
                $value = (string) $value;
            }

            if (!\is_string($value)) {
                throw new \InvalidArgumentException('The $data argument must contains a simple array of string values.');
            }
        }

        return $data;
    }
}
