<?php

declare(strict_types=1);

namespace Dew\Mns\Contracts;

use Psr\Http\Message\RequestInterface;

interface BuildsSignature
{
    /**
     * Build signature for the given request.
     */
    public function build(RequestInterface $request): string;
}
