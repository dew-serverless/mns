<?php

namespace Dew\Mns\Tests\Fixtures;

use Dew\Mns\Contracts\BuildsSignature;
use Psr\Http\Message\RequestInterface;

class StubSignature implements BuildsSignature
{
    public function __construct(
        public string $signature
    ) {
        //
    }

    public function build(RequestInterface $request): string
    {
        return $this->signature;
    }
}
