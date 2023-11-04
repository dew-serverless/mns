<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606;

use Dew\Mns\Versions\V20150606\Results\CreateTopicResult;
use Dew\Mns\Versions\V20150606\Results\GetSubscriptionAttributesResult;
use Dew\Mns\Versions\V20150606\Results\GetTopicAttributesResult;
use Dew\Mns\Versions\V20150606\Results\ListSubscriptionByTopicResult;
use Dew\Mns\Versions\V20150606\Results\ListTopicResult;
use Dew\Mns\Versions\V20150606\Results\PublishMessageResult;
use Dew\Mns\Versions\V20150606\Results\Result;
use Dew\Mns\Versions\V20150606\Results\SubscribeResult;

final class Topic extends Api
{
    /**
     * Create MNS topic.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function createTopic(string $topic, array $attributes = []): CreateTopicResult
    {
        $response = $this->put('/topics/'.$topic, data: ['Topic' => $attributes]);

        return new CreateTopicResult($response, $this->xml());
    }

    /**
     * Configure MNS topic.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function setTopicAttributes(string $topic, array $attributes = []): Result
    {
        $response = $this->put("/topics/$topic?metaoverride=true", data: ['Topic' => $attributes]);

        return new Result($response, $this->xml());
    }

    /**
     * Get MNS topic configuration.
     */
    public function getTopicAttributes(string $topic): GetTopicAttributesResult
    {
        $response = $this->get('/topics/'.$topic);

        return new GetTopicAttributesResult($response, $this->xml());
    }

    /**
     * Delete MNS topic.
     */
    public function deleteTopic(string $topic): Result
    {
        $response = $this->delete('/topics/'.$topic);

        return new Result($response, $this->xml());
    }

    /**
     * List topics by the given criteria.
     *
     * @param  array<string, string>  $criteria
     */
    public function listTopic(array $criteria = []): ListTopicResult
    {
        $response = $this->get('/topics', $criteria);

        return new ListTopicResult($response, $this->xml());
    }

    /**
     * Create subscription to the topic.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function subscribe(string $topic, string $subscription, array $attributes): SubscribeResult
    {
        $response = $this->put("/topics/$topic/subscriptions/$subscription", data: [
            'Subscription' => $attributes,
        ]);

        return new SubscribeResult($response, $this->xml());
    }

    /**
     * Configure subscription.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function setSubscriptionAttributes(string $topic, string $subscription, array $attributes): Result
    {
        $response = $this->put("/topics/$topic/subscriptions/$subscription?metaoverride=true", data: [
            'Subscription' => $attributes,
        ]);

        return new Result($response, $this->xml());
    }

    /**
     * Get subscription configuration.
     */
    public function getSubscriptionAttributes(string $topic, string $subscription): GetSubscriptionAttributesResult
    {
        $response = $this->get("/topics/$topic/subscriptions/$subscription");

        return new GetSubscriptionAttributesResult($response, $this->xml());
    }

    /**
     * Remove subscription from the topic.
     */
    public function unsubscribe(string $topic, string $subscription): Result
    {
        $response = $this->delete("/topics/$topic/subscriptions/$subscription");

        return new Result($response, $this->xml());
    }

    /**
     * List subscriptions by the given criteria.
     *
     * @param  array<string, string>  $criteria
     */
    public function listSubscriptionByTopic(string $topic, array $criteria = []): ListSubscriptionByTopicResult
    {
        $response = $this->get("/topics/$topic/subscriptions", $criteria);

        return new ListSubscriptionByTopicResult($response, $this->xml());
    }

    /**
     * Publish message to topic.
     *
     * @param  array<string, string>  $message
     */
    public function publishMessage(string $topic, array $message): PublishMessageResult
    {
        $response = $this->post("/topics/$topic/messages", data: [
            'Message' => $message,
        ]);

        return new PublishMessageResult($response, $this->xml());
    }
}
