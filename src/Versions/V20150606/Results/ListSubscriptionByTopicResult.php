<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;
use Dew\Mns\Versions\V20150606\Models\Subscription;

final class ListSubscriptionByTopicResult extends Result
{
    /**
     * The subscription list.
     *
     * @return Subscription[]|Subscription|null
     */
    public function subscriptions(int $index = null): array|Subscription|null
    {
        $subscriptions = $this->get('Subscription');

        if ($subscriptions === null) {
            return null;
        }

        if (! is_array($subscriptions)) {
            return null;
        }

        $subscriptions = Arr::wrapWith(Arr::list($subscriptions), Subscription::class);

        return Arr::get($subscriptions, $index);
    }

    /**
     * The pagination marker.
     */
    public function nextMarker(): ?string
    {
        return $this->string('NextMarker');
    }
}
