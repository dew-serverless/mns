<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Concerns;

use DateTimeInterface;

trait HasQueueAttributes
{
    public function queueName(): ?string
    {
        return $this->string('QueueName');
    }

    public function queueUrl(): ?string
    {
        return $this->string('QueueURL');
    }

    public function delaySeconds(): ?int
    {
        return $this->int('DelaySeconds');
    }

    public function maximumMessageSize(): ?int
    {
        return $this->int('MaximumMessageSize');
    }

    public function messageRetentionPeriod(): ?int
    {
        return $this->int('MessageRetentionPeriod');
    }

    public function pollingWaitSeconds(): ?int
    {
        return $this->int('PollingWaitSeconds');
    }

    public function inactiveMessages(): ?int
    {
        return $this->int('InactiveMessages');
    }

    public function activeMessages(): ?int
    {
        return $this->int('ActiveMessages');
    }

    public function delayMessages(): ?int
    {
        return $this->int('DelayMessages');
    }

    public function visibilityTimeout(): ?int
    {
        return $this->int('VisibilityTimeout');
    }

    public function loggingEnabled(): ?bool
    {
        return $this->bool('LoggingEnabled');
    }

    public function createTime(): ?DateTimeInterface
    {
        return $this->timestamp('CreateTime');
    }

    public function lastModifyTime(): ?DateTimeInterface
    {
        return $this->timestamp('LastModifyTime');
    }
}
