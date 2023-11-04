<?php

declare(strict_types=1);

namespace Dew\Mns\Contracts;

interface XmlEncoder
{
    /**
     * Encode data with XML document.
     *
     * @param  array<mixed>  $data
     */
    public function encode(array $data): string;

    /**
     * Decode data encoded with XML document.
     *
     * @return array<mixed>|string
     */
    public function decode(string $data): array|string;
}
