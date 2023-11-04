<?php

declare(strict_types=1);

namespace Dew\Mns\Concerns;

use Closure;

trait Extendable
{
    /**
     * The extended methods.
     *
     * @var array<string, Closure(mixed): mixed>
     */
    protected static array $extends = [];

    /**
     * Extend the class with the given method.
     *
     * @param  Closure(mixed): mixed  $callback
     */
    final public static function extend(string $method, Closure $callback): void
    {
        static::$extends[$method] = $callback;
    }

    /**
     * Determine if the class is being extended by the given method.
     */
    final public function hasExtend(string $method): bool
    {
        return array_key_exists($method, static::$extends);
    }

    /**
     * Call the extended method.
     *
     * @param  array<int, mixed>  $parameters
     */
    final protected function callExtend(string $method, array $parameters): mixed
    {
        $handler = static::$extends[$method]->bindTo($this);

        return $handler(...$parameters);
    }
}
