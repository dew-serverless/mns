<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use DateTimeInterface;

final class ChangeMessageVisibilityResult extends Result
{
    public function receiptHandle(): ?string
    {
        return $this->string('ReceiptHandle');
    }

    public function nextVisibleTime(): ?DateTimeInterface
    {
        return $this->timestampMs('NextVisibleTime');
    }
}
