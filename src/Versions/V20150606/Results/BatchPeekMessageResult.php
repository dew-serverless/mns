<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;
use Dew\Mns\Versions\V20150606\Models\Message;

final class BatchPeekMessageResult extends Result
{
    /**
     * The message list.
     *
     * @return Message[]|Message|null
     */
    public function messages(int $index = null): array|Message|null
    {
        $messages = $this->get('Message');

        if ($messages === null) {
            return null;
        }

        if (! is_array($messages)) {
            return null;
        }

        $messages = Arr::wrapWith(Arr::list($messages), Message::class);

        return Arr::get($messages, $index);
    }
}
