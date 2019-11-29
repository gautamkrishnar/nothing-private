<?php

declare(strict_types=1);

namespace Sentry\Context;

/**
 * This class stores generic information that will be attached to an event
 * being sent.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
class Context implements \ArrayAccess, \JsonSerializable, \IteratorAggregate
{
    /**
     * This constant defines the alias used for the user context.
     *
     * @deprecated To be removed in 3.0 because unused
     */
    public const CONTEXT_USER = 'user';

    /**
     * This constant defines the alias used for the runtime context.
     *
     * @deprecated To be removed in 3.0 because unused
     */
    public const CONTEXT_RUNTIME = 'runtime';

    /**
     * This constant defines the alias used for the tags context.
     *
     * @deprecated To be removed in 3.0 because unused
     */
    public const CONTEXT_TAGS = 'tags';

    /**
     * This constant defines the alias used for the extra context.
     *
     * @deprecated To be removed in 3.0 because unused
     */
    public const CONTEXT_EXTRA = 'extra';

    /**
     * This constant defines the alias used for the server OS context.
     *
     * @deprecated To be removed in 3.0 because unused
     */
    public const CONTEXT_SERVER_OS = 'server_os';

    /**
     * @var array The data stored in this object
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param array $data The initial data to store
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Merges the given data with the existing one, recursively or not, according
     * to the value of the `$recursive` parameter.
     *
     * @param array $data      The data to merge
     * @param bool  $recursive Whether to merge the data recursively or not
     */
    public function merge(array $data, bool $recursive = false): void
    {
        $this->data = $recursive ? array_merge_recursive($this->data, $data) : array_merge($this->data, $data);
    }

    /**
     * Sets each element of the array to the value of the corresponding key in
     * the given input data.
     *
     * @param array $data The data to set
     */
    public function setData(array $data): void
    {
        foreach ($data as $index => $value) {
            $this->data[$index] = $value;
        }
    }

    /**
     * Replaces all the data contained in this object with the given one.
     *
     * @param array $data The data to set
     */
    public function replaceData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Clears the entire data contained in this object.
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Checks whether the object is not storing any data.
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Returns an array representation of the data stored by the object.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]) || \array_key_exists($offset, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
