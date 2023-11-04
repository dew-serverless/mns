<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Concerns;

use DateTimeImmutable;
use DateTimeInterface;

trait HasAttributes
{
    /**
     * Get the data as string value.
     */
    final public function string(string $key, string $default = null): ?string
    {
        $value = $this->get($key);

        if ($value === null) {
            return $default;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * Get the data as integer.
     */
    final public function int(string $key, int $default = null): ?int
    {
        $value = $this->get($key);

        if ($value === null) {
            return $default;
        }

        if (is_scalar($value)) {
            return (int) $value;
        }

        return $default;
    }

    /**
     * Get the data as boolean value.
     */
    final public function bool(string $key, bool $default = null): ?bool
    {
        $value = $this->get($key);

        if ($value === null) {
            return $default;
        }

        if (is_string($value)) {
            return strtolower($value) === 'true';
        }

        return (bool) $value;
    }

    /**
     * Get the timestamp value as datetime.
     */
    final public function timestamp(string $key, DateTimeInterface $default = null): ?DateTimeInterface
    {
        $value = $this->get($key);

        if (is_string($value)) {
            $value = DateTimeImmutable::createFromFormat('U', $value);
        }

        return $value instanceof DateTimeInterface ? $value : $default;
    }

    /**
     * Get the timestamp value in milliseconds as datetime.
     */
    final public function timestampMs(string $key, DateTimeInterface $default = null): ?DateTimeInterface
    {
        $value = $this->get($key);

        if (is_string($value)) {
            [$seconds, $milliseconds] = [substr($value, 0, -3), substr($value, -3)];
            $seconds = $seconds === '' ? 0 : $seconds;
            $milliseconds = $milliseconds === '' ? 0 : $milliseconds;
            $value = DateTimeImmutable::createFromFormat('U u', $seconds.' '.$milliseconds);
        }

        return $value instanceof DateTimeInterface ? $value : $default;
    }
}
