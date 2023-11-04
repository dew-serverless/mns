<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Topic;

beforeEach(function () {
    $this->mns = new MnsClient(getenv('MNS_ENDPOINT'), getenv('ACS_ACCESS_KEY_ID'), getenv('ACS_ACCESS_KEY_SECRET'));
    $this->topic = new Topic($this->mns);
    $this->topicName = getenv('MNS_TOPIC_NAME');
    $this->subscriptionName = 'webhook';
})->skip(! integrationTestEnabled(), 'Integration test is not enabled.');

test('create topic', function () {
    $result = $this->topic->createTopic($this->topicName);
    expect($result->successful())->toBeTrue()
        ->and($result->topicUrl())->toBeString()->toContain('http://');
});

test('set topic attributes', function () {
    $result = $this->topic->setTopicAttributes($this->topicName, ['LoggingEnabled' => 'True']);
    expect($result->successful())->toBeTrue();
})->depends('create topic');

test('get topic attributes', function () {
    $result = $this->topic->getTopicAttributes($this->topicName);
    expect($result->successful())->toBetrue()
        ->and($result->loggingEnabled())->toBeTrue();
})->depends('set topic attributes');

test('list topic', function () {
    $result = $this->topic->listTopic(['x-mns-prefix' => $this->topicName]);
    expect($result->successful())->toBeTrue()
        ->and($result->topics())->toBeList()
        ->and($result->topics(0)->topicName())->toBe($this->topicName);
})->depends('create topic');

test('subscribe', function () {
    $result = $this->topic->subscribe($this->topicName, $this->subscriptionName, ['Endpoint' => 'https://zhineng.li']);
    expect($result->successful())->toBeTrue()
        ->and($result->subscriptionUrl())->toBeString()->toContain('aliyuncs.com');
})->depends('create topic');

test('set subscription attributes', function () {
    $result = $this->topic->setSubscriptionAttributes($this->topicName, $this->subscriptionName, ['NotifyStrategy' => 'EXPONENTIAL_DECAY_RETRY']);
    expect($result->successful())->toBeTrue();
})->depends('subscribe');

test('get subscription attributes', function () {
    $result = $this->topic->getSubscriptionAttributes($this->topicName, $this->subscriptionName);
    expect($result->successful())->toBeTrue()
        ->and($result->subscriptionName())->toBe($this->subscriptionName)
        ->and($result->notifyStrategy())->toBe('EXPONENTIAL_DECAY_RETRY');
})->depends('set subscription attributes');

test('list subscription by topic', function () {
    $result = $this->topic->listSubscriptionByTopic($this->topicName, ['x-mns-prefix' => $this->subscriptionName]);
    expect($result->successful())->toBeTrue()
        ->and($result->subscriptions())->toBeList()
        ->and($result->subscriptions(0)->subscriptionName())->toBe($this->subscriptionName);
})->depends('subscribe');

test('publish message', function () {
    $result = $this->topic->publishMessage($this->topicName, ['MessageBody' => 'Hello world!']);
    expect($result->successful())->toBeTrue()
        ->and($result->messageId())->toBeString()->not->toBeEmpty();
})->depends('subscribe');

test('unsubscribe', function () {
    $result = $this->topic->unsubscribe($this->topicName, $this->subscriptionName);
    expect($result->successful())->toBeTrue();
})->depends('subscribe');

test('delete topic', function () {
    $result = $this->topic->deleteTopic($this->topicName);
    expect($result->successful())->toBeTrue();
})->depends('create topic');
