<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Models\Queue as QueueModel;
use Dew\Mns\Versions\V20150606\Queue;

beforeEach(function () {
    $this->mns = new MnsClient(getenv('MNS_ENDPOINT'), getenv('ACS_ACCESS_KEY_ID'), getenv('ACS_ACCESS_KEY_SECRET'));
    $this->queue = new Queue($this->mns);
    $this->queueName = getenv('MNS_QUEUE_NAME');
})->skip(! integrationTestEnabled(), 'Integration test is not enabled.');

test('create queue', function () {
    $result = $this->queue->createQueue($this->queueName);
    expect($result->successful())->toBeTrue()
        ->and($result->queueUrl())->toBeString()->toStartWith('http://');
});

test('set queue attributes', function () {
    $result = $this->queue->setQueueAttributes($this->queueName, ['VisibilityTimeout' => '600']);
    expect($result->successful())->toBeTrue();
})->depends('create queue');

test('get queue attributes', function () {
    $result = $this->queue->getQueueAttributes($this->queueName);
    expect($result->successful())->toBeTrue()
        ->and($result->visibilityTimeout())->toBe(600);
})->depends('set queue attributes');

test('list queue', function () {
    $result = $this->queue->listQueue(['x-mns-prefix' => $this->queueName]);
    expect($result->successful())->toBeTrue()
        ->and($result->queues(0))->toBeInstanceOf(QueueModel::class)
        ->and($result->queues(0)->queueName())->toBe($this->queueName);
})->depends('create queue');

test('send message', function () {
    $result = $this->queue->sendMessage($this->queueName, ['MessageBody' => 'Hello world!']);
    expect($result->successful())->toBeTrue()
        ->and($result->messageId())->toBeString()->not->toBeEmpty();
})->depends('create queue');

test('batch send message', function () {
    $result = $this->queue->batchSendMessage($this->queueName, [['MessageBody' => 'Message 1'], ['MessageBody' => 'Message 2']]);
    expect($result->successful())->toBeTrue()
        ->and($result->messages())->toBeArray()->toHaveCount(2);
})->depends('create queue');

test('receive message', function () {
    $result = $this->queue->receiveMessage($this->queueName);
    expect($result->successful())->toBeTrue()
        ->and($result->receiptHandle())->toBeString()->not->toBeEmpty();

    return $result->receiptHandle();
})->depends('send message');

test('batch receive message', function () {
    $result = $this->queue->batchReceiveMessage($this->queueName, ['numOfMessages' => '16']);
    expect($result->successful())->toBeTrue()
        ->and($result->messages())->toBeList()->not->toBeEmpty();

    return array_map(fn ($message) => $message->receiptHandle(), $result->messages());
})->depends('batch send message');

test('delete message', function ($receipt) {
    $result = $this->queue->deleteMessage($this->queueName, $receipt);
    expect($result->successful())->toBeTrue();
})->depends('change message visibility');

test('batch delete message', function ($receipts) {
    $result = $this->queue->batchDeleteMessage($this->queueName, $receipts);
    expect($result->successful())->toBeTrue();
})->depends('batch receive message');

test('peek message', function () {
    $this->queue->sendMessage($this->queueName, ['MessageBody' => 'Hello world!']);
    $result = $this->queue->peekMessage($this->queueName);
    expect($result->successful())->toBeTrue()
        ->and($result->messageId())->toBeString()->not->toBeEmpty()
        ->and($result->receiptHandle())->toBeNull();
})->depends('create queue');

test('batch peek message', function () {
    $this->queue->batchSendMessage($this->queueName, [['MessageBody' => 'Message 1'], ['MessageBody' => 'Message 2']]);
    $result = $this->queue->batchPeekMessage($this->queueName, 16);
    expect($result->successful())->toBeTrue()
        ->and($result->messages())->toBeList()->not->toBeEmpty();
})->depends('create queue');

test('change message visibility', function ($receipt) {
    $result = $this->queue->changeMessageVisibility($this->queueName, $receipt, 600);
    expect($result->successful())->toBeTrue();

    return $result->receiptHandle();
})->depends('receive message');

test('delete queue', function () {
    $result = $this->queue->deleteQueue($this->queueName);
    expect($result->successful())->toBeTrue();
})->depends('create queue');
