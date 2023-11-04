<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

final class SubscribeResult extends Result
{
    /**
     * The subscription location.
     */
    public function location(): string
    {
        return $this->response->getHeaderLine('location');
    }

    public function subscriptionUrl(): string
    {
        return $this->location();
    }
}
