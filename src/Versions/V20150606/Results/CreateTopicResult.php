<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

final class CreateTopicResult extends Result
{
    /**
     * The topic URL.
     */
    public function location(): string
    {
        return $this->response->getHeaderLine('location');
    }

    public function topicUrl(): string
    {
        return $this->location();
    }
}
