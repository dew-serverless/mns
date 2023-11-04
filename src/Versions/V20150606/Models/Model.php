<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Models;

use ArrayAccess;
use BadMethodCallException;
use Dew\Mns\Concerns\Extendable;
use Dew\Mns\Support\Arr;
use Dew\Mns\Versions\V20150606\Concerns\HasAttributes;
use LogicException;

/**
 * @implements ArrayAccess<mixed, mixed>
 */
abstract class Model implements ArrayAccess
{
    use Extendable;
    use HasAttributes;

    /**
     * Create a model.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        protected array $attributes = []
    ) {
        //
    }

    /**
     * Get the data with dot notation.
     */
    final public function get(string $key = null, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Determine if an array offset exists.
     */
    final public function offsetExists(mixed $offset): bool
    {
        $data = $this->get();

        if (! is_array($data)) {
            return false;
        }

        if (is_string($offset) || is_int($offset)) {
            return Arr::has($data, $offset);
        }

        return false;
    }

    /**
     * Get the data through array syntax.
     */
    final public function offsetGet(mixed $offset): mixed
    {
        if (is_scalar($offset)) {
            return $this->get((string) $offset);
        }

        return null;
    }

    /**
     * Set the data through array syntax.
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException('Could not mutate the model data.');
    }

    /**
     * Unset the data through array syntax.
     */
    final public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('Could not mutate the model data.');
    }

    /**
     * Handle inaccessable methods.
     *
     * @param  array<int, mixed>  $parameters
     */
    final public function __call(string $method, array $parameters): mixed
    {
        if (static::hasExtend($method)) {
            return static::callExtend($method, $parameters);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
    }
}
