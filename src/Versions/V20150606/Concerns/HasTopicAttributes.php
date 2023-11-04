<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Concerns;

use DateTimeInterface;

trait HasTopicAttributes
{
    public function topicName(): ?string
    {
        return $this->string('TopicName');
    }

    public function topicUrl(): ?string
    {
        return $this->string('TopicURL');
    }

    public function maximumMessageSize(): ?int
    {
        return $this->int('MaximumMessageSize');
    }

    public function messageRetentionPeriod(): ?int
    {
        return $this->int('MessageRetentionPeriod');
    }

    public function messageCount(): ?int
    {
        return $this->int('MessageCount');
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
