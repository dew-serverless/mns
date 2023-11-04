<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

final class CreateQueueResult extends Result
{
    /**
     * The queue URL.
     */
    public function location(): string
    {
        return $this->response->getHeaderLine('location');
    }

    public function queueUrl(): string
    {
        return $this->location();
    }
}
