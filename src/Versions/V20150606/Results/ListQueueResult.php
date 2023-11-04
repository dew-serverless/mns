<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;
use Dew\Mns\Versions\V20150606\Models\Queue;

final class ListQueueResult extends Result
{
    /**
     * The queue list.
     *
     * @return Queue[]|Queue|null
     */
    public function queues(int $index = null): array|Queue|null
    {
        $queues = $this->get('Queue');

        if (is_null($queues)) {
            return null;
        }

        if (! is_array($queues)) {
            return null;
        }

        $queues = Arr::wrapWith(Arr::list($queues), Queue::class);

        return Arr::get($queues, $index);
    }

    /**
     * The pagination marker.
     */
    public function nextMarker(): ?string
    {
        return $this->string('NextMarker');
    }
}
