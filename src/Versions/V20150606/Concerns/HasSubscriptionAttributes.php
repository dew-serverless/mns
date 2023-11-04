<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Concerns;

use DateTimeInterface;

trait HasSubscriptionAttributes
{
    public function topicName(): ?string
    {
        return $this->string('TopicName');
    }

    public function topicOwner(): ?string
    {
        return $this->string('TopicOwner');
    }

    public function subscriptionName(): ?string
    {
        return $this->string('SubscriptionName');
    }

    public function subscriptionUrl(): ?string
    {
        return $this->string('SubscriptionURL');
    }

    public function subscriber(): ?string
    {
        return $this->string('Subscriber');
    }

    public function filterTag(): ?string
    {
        return $this->string('FilterTag');
    }

    public function endpoint(): ?string
    {
        return $this->string('Endpoint');
    }

    public function notifyStrategy(): ?string
    {
        return $this->string('NotifyStrategy');
    }

    public function notifyContentFormat(): ?string
    {
        return $this->string('NotifyContentFormat');
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
