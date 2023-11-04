<?php

declare(strict_types=1);

namespace Dew\Mns\Support;

final class Arr
{
    /**
     * Wrap the given array into list if one is an associative.
     *
     * @param  array<int, mixed>|array<string, mixed>  $array
     * @return array<int, mixed>
     */
    public static function list(array $array): array
    {
        if (! array_is_list($array)) {
            return [$array];
        }

        return $array;
    }

    /**
     * Wrap each of the array item into class.
     *
     * @template T of object
     *
     * @param  array<int, mixed>  $array
     * @param  class-string<T>  $class
     * @return T[]
     */
    public static function wrapWith(array $array, string $class): array
    {
        return array_map(fn ($item): object => new $class($item), $array);
    }

    /**
     * Set array with dot notation.
     *
     * @param  array<mixed>  $array
     * @return array<mixed>
     */
    public static function set(array &$array, string $key, mixed $value): array
    {
        $keys = explode('.', $key);

        foreach ($keys as $i => $k) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            $array[$k] ??= null;

            if (! is_array($array[$k])) {
                $array[$k] = [];
            }

            $array = &$array[$k];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Determine if the array has the given key.
     *
     * @param  array<mixed>  $array
     */
    public static function has(array $array, string|int $key): bool
    {
        $key = (string) $key;

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get array with dot notation.
     *
     * @template T
     * @template TDefault
     *
     * @param  array<T>  $array
     * @param  TDefault  $default
     * @return ($key is null ? array<T> : T|TDefault)
     */
    public static function get(array $array, string|int $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        }

        $key = (string) $key;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
