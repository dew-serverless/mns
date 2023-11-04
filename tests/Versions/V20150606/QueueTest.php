<?php

use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Queue;
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

beforeEach(function () {
    $this->mockedXml = Mockery::mock(XmlEncoder::class);
    $this->endpoint = 'http://1234567891011.mns.cn-hangzhou.aliyuncs.com';
    $this->mns = new MnsClient($this->endpoint, 'key', 'secret');
    $this->mns->fake();
    $this->queue = new Queue($this->mns);
    $this->queue->xmlUsing($this->mockedXml);
});

test('create queue', function () {
    $this->mockedXml->expects()->encode(['Queue' => ['VisibilityTimeout' => 600]]);
    $result = $this->queue->createQueue('queue-name', ['VisibilityTimeout' => 600]);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name'
    );
    expect($result)->toBeInstanceOf(CreateQueueResult::class);
});

test('set queue attributes', function () {
    $this->mockedXml->expects()->encode(['Queue' => ['VisibilityTimeout' => 600]]);
    $result = $this->queue->setQueueAttributes('queue-name', ['VisibilityTimeout' => 600]);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name?metaoverride=true'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('get queue attributes', function () {
    $result = $this->queue->getQueueAttributes('queue-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name'
    );
    expect($result)->toBeInstanceOf(GetQueueAttributesResult::class);
});

test('delete queue', function () {
    $result = $this->queue->deleteQueue('queue-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'DELETE' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('list queue', function () {
    $result = $this->queue->listQueue(['x-mns-prefix' => 'queue']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues' &&
        $request->getHeaderLine('x-mns-prefix') === 'queue'
    );
    expect($result)->toBeInstanceOf(ListQueueResult::class);
});

test('list queue normalizes queue list', function () {
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Queue' => ['QueueURL' => $this->endpoint.'/queues/queue-1']]);
    $result = $this->queue->listQueue(['x-mns-prefix' => 'queue']);
    expect($result)->toBeInstanceOf(ListQueueResult::class)
        ->and($result->queues())->toBeList()
        ->and($result->queues(0)->queueUrl())->toBe($this->endpoint.'/queues/queue-1');
});

test('send message', function () {
    $this->mockedXml->expects()->encode(['Message' => ['MessageBody' => 'Hello world!']]);
    $result = $this->queue->sendMessage('queue-name', ['MessageBody' => 'Hello world!']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'POST' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages'
    );
    expect($result)->toBeInstanceOf(SendMessageResult::class);
});

test('batch send message', function () {
    $messages = [['MessageBody' => 'foo'], ['MessageBody' => 'bar']];
    $this->mockedXml->expects()->encode(['Messages' => ['Message' => $messages]]);
    $result = $this->queue->batchSendMessage('queue-name', $messages);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'POST' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages'
    );
    expect($result)->toBeInstanceOf(BatchSendMessageResult::class);
});

test('batch send message normalizes message list', function () {
    $messages = [['MessageBody' => 'foo']];
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->encode(['Messages' => ['Message' => $messages]]);
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Message' => ['MessageId' => '5F290C926D472878-2-14D9529A8FA-20000****']]);
    $result = $this->queue->batchSendMessage('queue-name', $messages);
    expect($result)->toBeInstanceOf(BatchSendMessageResult::class)
        ->and($result->messages())->toBeList()
        ->and($result->messages(0)['MessageId'])->toBe('5F290C926D472878-2-14D9529A8FA-20000****');
});

test('receive message', function () {
    $result = $this->queue->receiveMessage('queue-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages'
    );
    expect($result)->toBeInstanceOf(ReceiveMessageResult::class);
});

test('receive message with long polling', function () {
    $result = $this->queue->receiveMessage('queue-name', ['waitseconds' => '10']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?waitseconds=10'
    );
    expect($result)->toBeInstanceOf(ReceiveMessageResult::class);
});

test('batch receive message', function () {
    $result = $this->queue->batchReceiveMessage('queue-name', ['numOfMessages' => 10]);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?numOfMessages=10'
    );
    expect($result)->toBeInstanceOf(BatchReceiveMessageResult::class);
});

test('batch receive message normalizes message list', function () {
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Message' => ['MessageId' => '5F290C926D472878-2-14D9529A8FA-20000****']]);
    $result = $this->queue->batchReceiveMessage('queue-name', ['numOfMessages' => 10]);
    expect($result)->toBeInstanceOf(BatchReceiveMessageResult::class)
        ->and($result->messages())->toBeList()
        ->and($result->messages(0)->messageId())->toBe('5F290C926D472878-2-14D9529A8FA-20000****');
});

test('delete message', function () {
    $result = $this->queue->deleteMessage('queue-name', 'foo');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'DELETE' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?ReceiptHandle=foo'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('batch delete message', function () {
    $receipts = ['receipt-1', 'receipt-2'];
    $this->mockedXml->expects()->encode(['ReceiptHandles' => ['ReceiptHandle' => $receipts]]);
    $result = $this->queue->batchDeleteMessage('queue-name', $receipts);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'DELETE' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages'
    );
    expect($result)->toBeInstanceOf(BatchDeleteMessageResult::class);
});

test('batch delete message normalizes error list', function () {
    $receipts = ['receipt-1'];
    $this->mns->fake(xmlResponse(204));
    $this->mockedXml->expects()->encode(['ReceiptHandles' => ['ReceiptHandle' => $receipts]]);
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Error' => ['ErrorCode' => 'MessageNotExist']]);
    $result = $this->queue->batchDeleteMessage('queue-name', $receipts);
    expect($result)->toBeInstanceOf(BatchDeleteMessageResult::class)
        ->and($result->errors())->toBeList()
        ->and($result->errors(0)['ErrorCode'])->toBe('MessageNotExist');
});

test('peek message', function () {
    $result = $this->queue->peekMessage('queue-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?peekonly=true'
    );
    expect($result)->toBeInstanceOf(PeekMessageResult::class);
});

test('batch peek message', function () {
    $result = $this->queue->batchPeekMessage('queue-name', 16);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?peekonly=true&numOfMessages=16'
    );
    expect($result)->toBeInstanceOf(BatchPeekMessageResult::class);
});

test('batch peek message normalizes message list', function () {
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Message' => ['MessageId' => 'D6D5F7C9C12D14A4-1-14D953EFC72-20000****']]);
    $result = $this->queue->batchPeekMessage('queue-name', 16);
    expect($result)->toBeInstanceOf(BatchPeekMessageResult::class)
        ->and($result->messages())->toBeList()
        ->and($result->messages(0)->messageId())->toBe('D6D5F7C9C12D14A4-1-14D953EFC72-20000****');
});

test('change message visibility', function () {
    $result = $this->queue->changeMessageVisibility('queue-name', 'receipt', 60);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/queues/queue-name/messages?receiptHandle=receipt&visibilityTimeout=60'
    );
    expect($result)->toBeInstanceOf(ChangeMessageVisibilityResult::class);
});
