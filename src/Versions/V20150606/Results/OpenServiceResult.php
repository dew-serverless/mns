<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

final class OpenServiceResult extends Result
{
    /**
     * The service activation order ID.
     */
    public function orderId(): ?string
    {
        return $this->string('OrderId');
    }
}
