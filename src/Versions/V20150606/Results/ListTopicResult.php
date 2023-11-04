<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;
use Dew\Mns\Versions\V20150606\Models\Topic;

final class ListTopicResult extends Result
{
    /**
     * The topic list.
     *
     * @return Topic[]|Topic|null
     */
    public function topics(int $index = null): array|Topic|null
    {
        $topics = $this->get('Topic');

        if ($topics === null) {
            return null;
        }

        if (! is_array($topics)) {
            return null;
        }

        $topics = Arr::wrapWith(Arr::list($topics), Topic::class);

        return Arr::get($topics, $index);
    }

    /**
     * The pagination marker.
     */
    public function nextMarker(): ?string
    {
        return $this->string('NextMarker');
    }
}
