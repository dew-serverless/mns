<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Concerns;

use DateTimeInterface;

trait HasMessageAttributes
{
    public function messageId(): ?string
    {
        return $this->string('MessageId');
    }

    public function messageBody(): ?string
    {
        return $this->string('MessageBody');
    }

    public function messageBodyMd5(): ?string
    {
        return $this->string('MessageBodyMD5');
    }

    public function receiptHandle(): ?string
    {
        return $this->string('ReceiptHandle');
    }

    public function priority(): ?int
    {
        return $this->int('Priority');
    }

    public function dequeueCount(): ?int
    {
        return $this->int('DequeueCount');
    }

    public function enqueueTime(): ?DateTimeInterface
    {
        return $this->timestampMs('EnqueueTime');
    }

    public function nextVisibleTime(): ?DateTimeInterface
    {
        return $this->timestampMs('NextVisibleTime');
    }

    public function firstDequeueTime(): ?DateTimeInterface
    {
        return $this->timestampMs('FirstDequeueTime');
    }
}
