<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Support\Arr;

/**
 * @phpstan-type Error array{ErrorCode: string, ErrorMessage: string, ReceiptHandle: string}
 */
final class BatchDeleteMessageResult extends Result
{
    /**
     * The message deletion errors.
     *
     * @return Error[]|Error|null
     */
    public function errors(int $index = null): ?array
    {
        $errors = $this->get('Error');

        if ($errors === null) {
            return null;
        }

        if (! is_array($errors)) {
            return null;
        }

        /** @var Error[] */
        $errors = Arr::list($errors);

        return Arr::get($errors, $index);
    }
}
