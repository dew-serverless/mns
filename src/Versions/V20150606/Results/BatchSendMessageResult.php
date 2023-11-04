<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;

/**
 * @phpstan-type Message array{MessageId: string, MessageBodyMD5: string}
 */
final class BatchSendMessageResult extends Result
{
    /**
     * The sent messages.
     *
     * @return Message[]|Message|null
     */
    public function messages(int $index = null): ?array
    {
        $messages = $this->get('Message');

        if ($messages === null) {
            return null;
        }

        if (! is_array($messages)) {
            return null;
        }

        /** @var Message[] */
        $messages = Arr::list($messages);

        return Arr::get($messages, $index);
    }
}
