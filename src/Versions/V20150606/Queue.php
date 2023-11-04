<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606;

use Dew\Mns\Versions\V20150606\Results\BatchDeleteMessageResult;
use Dew\Mns\Versions\V20150606\Results\BatchPeekMessageResult;
use Dew\Mns\Versions\V20150606\Results\BatchReceiveMessageResult;
use Dew\Mns\Versions\V20150606\Results\BatchSendMessageResult;
use Dew\Mns\Versions\V20150606\Results\ChangeMessageVisibilityResult;
use Dew\Mns\Versions\V20150606\Results\CreateQueueResult;
use Dew\Mns\Versions\V20150606\Results\GetQueueAttributesResult;
use Dew\Mns\Versions\V20150606\Results\ListQueueResult;
use Dew\Mns\Versions\V20150606\Results\PeekMessageResult;
use Dew\Mns\Versions\V20150606\Results\ReceiveMessageResult;
use Dew\Mns\Versions\V20150606\Results\Result;
use Dew\Mns\Versions\V20150606\Results\SendMessageResult;

final class Queue extends Api
{
    /**
     * Create a new MNS queue.
     *
     * @param  array<string, string>  $attributes
     */
    public function createQueue(string $queue, array $attributes = []): CreateQueueResult
    {
        $response = $this->put('/queues/'.$queue, data: ['Queue' => $attributes]);

        return new CreateQueueResult($response, $this->xml());
    }

    /**
     * Configure MNS queue.
     *
     * @param  array<string, string>  $attributes
     */
    public function setQueueAttributes(string $queue, array $attributes): Result
    {
        $response = $this->put(sprintf('/queues/%s?metaoverride=true', $queue), data: [
            'Queue' => $attributes,
        ]);

        return new Result($response, $this->xml());
    }

    /**
     * Get MNS queue configuration.
     */
    public function getQueueAttributes(string $queue): GetQueueAttributesResult
    {
        $response = $this->get('/queues/'.$queue);

        return new GetQueueAttributesResult($response, $this->xml());
    }

    /**
     * Delete the MNS queue.
     */
    public function deleteQueue(string $queue): Result
    {
        $response = $this->delete('/queues/'.$queue);

        return new Result($response, $this->xml());
    }

    /**
     * List queues by the given criteria.
     *
     * @param  array<string, string>  $attributes
     */
    public function listQueue(array $attributes = []): ListQueueResult
    {
        $response = $this->get('/queues', $attributes);

        return new ListQueueResult($response, $this->xml());
    }

    /**
     * Send a message to queue.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function sendMessage(string $queue, array $attributes): SendMessageResult
    {
        $response = $this->post('/queues/'.$queue.'/messages', data: [
            'Message' => $attributes,
        ]);

        return new SendMessageResult($response, $this->xml());
    }

    /**
     * Send multiple messages to queue.
     *
     * @param  array<int, array<string, mixed>>  $messages
     */
    public function batchSendMessage(string $queue, array $messages): BatchSendMessageResult
    {
        $response = $this->post('/queues/'.$queue.'/messages', data: [
            'Messages' => ['Message' => $messages],
        ]);

        return new BatchSendMessageResult($response, $this->xml());
    }

    /**
     * Get a message from queue.
     *
     * @param  array<string, string>  $attributes
     */
    public function receiveMessage(string $queue, array $attributes = []): ReceiveMessageResult
    {
        $response = $this->get('/queues/'.$queue.'/messages', data: $attributes);

        return new ReceiveMessageResult($response, $this->xml());
    }

    /**
     * Get multiple messages from queue.
     *
     * @param  array<string, string>  $attributes
     */
    public function batchReceiveMessage(string $queue, array $attributes = []): BatchReceiveMessageResult
    {
        $response = $this->get('/queues/'.$queue.'/messages', data: $attributes);

        return new BatchReceiveMessageResult($response, $this->xml());
    }

    /**
     * Delete a message from queue.
     */
    public function deleteMessage(string $queue, string $receiptHandle): Result
    {
        $response = $this->delete('/queues/'.$queue.'/messages?ReceiptHandle='.$receiptHandle);

        return new Result($response, $this->xml());
    }

    /**
     * Delete multiple messages from queue.
     *
     * @param  array<int, string>  $receipts
     */
    public function batchDeleteMessage(string $queue, array $receipts): BatchDeleteMessageResult
    {
        $response = $this->delete('/queues/'.$queue.'/messages', data: [
            'ReceiptHandles' => ['ReceiptHandle' => $receipts],
        ]);

        return new BatchDeleteMessageResult($response, $this->xml());
    }

    /**
     * Peek a message from queue.
     */
    public function peekMessage(string $queue): PeekMessageResult
    {
        $response = $this->get('/queues/'.$queue.'/messages', data: ['peekonly' => 'true']);

        return new PeekMessageResult($response, $this->xml());
    }

    /*
     * Peek multiple messages from queue.
     */
    public function batchPeekMessage(string $queue, int $maxMessages): BatchPeekMessageResult
    {
        $response = $this->get('/queues/'.$queue.'/messages', data: [
            'peekonly' => 'true',
            'numOfMessages' => (string) $maxMessages,
        ]);

        return new BatchPeekMessageResult($response, $this->xml());
    }

    /**
     * Update message next available time.
     */
    public function changeMessageVisibility(string $queue, string $receipt, int $seconds): ChangeMessageVisibilityResult
    {
        $response = $this->put(sprintf('/queues/%s/messages?receiptHandle=%s&visibilityTimeout=%s',
            $queue, $receipt, $seconds
        ));

        return new ChangeMessageVisibilityResult($response, $this->xml());
    }
}
