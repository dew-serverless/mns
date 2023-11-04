<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

final class PublishMessageResult extends Result
{
    public function messageId(): ?string
    {
        return $this->string('MessageId');
    }

    public function messageBodyMd5(): ?string
    {
        return $this->string('MessageBodyMD5');
    }
}
