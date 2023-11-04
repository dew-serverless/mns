<?php

use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Results\CreateTopicResult;
use Dew\Mns\Versions\V20150606\Results\GetSubscriptionAttributesResult;
use Dew\Mns\Versions\V20150606\Results\GetTopicAttributesResult;
use Dew\Mns\Versions\V20150606\Results\ListSubscriptionByTopicResult;
use Dew\Mns\Versions\V20150606\Results\ListTopicResult;
use Dew\Mns\Versions\V20150606\Results\PublishMessageResult;
use Dew\Mns\Versions\V20150606\Results\Result;
use Dew\Mns\Versions\V20150606\Results\SubscribeResult;
use Dew\Mns\Versions\V20150606\Topic;

beforeEach(function () {
    $this->mockedXml = Mockery::mock(XmlEncoder::class);
    $this->endpoint = 'http://1234567891011.mns.cn-hangzhou.aliyuncs.com';
    $this->mns = new MnsClient($this->endpoint, 'key', 'secret');
    $this->mns->fake();
    $this->topic = new Topic($this->mns);
    $this->topic->xmlUsing($this->mockedXml);
});

test('create topic', function () {
    $this->mockedXml->expects()->encode(['Topic' => ['MaximumMessageSize' => 65536]]);
    $result = $this->topic->createTopic('topic-name', ['MaximumMessageSize' => 65536]);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name'
    );
    expect($result)->toBeInstanceOf(CreateTopicResult::class);
});

test('set topic attributes', function () {
    $this->mockedXml->expects()->encode(['Topic' => ['MaximumMessageSize' => 65536]]);
    $result = $this->topic->setTopicAttributes('topic-name', ['MaximumMessageSize' => 65536]);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name?metaoverride=true'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('get topic attributes', function () {
    $result = $this->topic->getTopicAttributes('topic-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name'
    );
    expect($result)->toBeInstanceOf(GetTopicAttributesResult::class);
});

test('delete topic', function () {
    $result = $this->topic->deleteTopic('topic-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'DELETE' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('list topic', function () {
    $result = $this->topic->listTopic(['x-mns-prefix' => 'topic']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/topics' &&
        $request->getHeaderLine('x-mns-prefix') === 'topic'
    );
    expect($result)->toBeInstanceOf(ListTopicResult::class);
});

test('list topic normalizes topic list', function () {
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Topic' => ['TopicURL' => 'http://1234567891011.mns.cn-hangzhou.aliyuncs.com/topics/topic-1']]);
    $result = $this->topic->listTopic(['x-mns-prefix' => 'topic']);
    expect($result)->toBeInstanceOf(ListTopicResult::class)
        ->and($result->topics())->toBeList()
        ->and($result->topics(0)->topicUrl())->toBe('http://1234567891011.mns.cn-hangzhou.aliyuncs.com/topics/topic-1');
});

test('subscribe', function () {
    $this->mockedXml->expects()->encode(['Subscription' => ['Endpoint' => 'https://example.com']]);
    $result = $this->topic->subscribe('topic-name', 'subscription-name', ['Endpoint' => 'https://example.com']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/subscriptions/subscription-name'
    );
    expect($result)->toBeInstanceOf(SubscribeResult::class);
});

test('set subscription attributes', function () {
    $this->mockedXml->expects()->encode(['Subscription' => ['NotifyStrategy' => 'BACKOFF_RETRY']]);
    $result = $this->topic->setSubscriptionAttributes('topic-name', 'subscription-name', ['NotifyStrategy' => 'BACKOFF_RETRY']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'PUT' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/subscriptions/subscription-name?metaoverride=true'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('get subscription attributes', function () {
    $result = $this->topic->getSubscriptionAttributes('topic-name', 'subscription-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/subscriptions/subscription-name'
    );
    expect($result)->toBeInstanceOf(GetSubscriptionAttributesResult::class);
});

test('unsubscribe', function () {
    $result = $this->topic->unsubscribe('topic-name', 'subscription-name');
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'DELETE' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/subscriptions/subscription-name'
    );
    expect($result)->toBeInstanceOf(Result::class);
});

test('list subscription by topic', function () {
    $result = $this->topic->listSubscriptionByTopic('topic-name', ['x-mns-prefix' => 'topic']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'GET' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/subscriptions' &&
        $request->getHeaderLine('x-mns-prefix') === 'topic'
    );
    expect($result)->toBeInstanceOf(ListSubscriptionByTopicResult::class);
});

test('list subscription by topic normalizes subscription list', function () {
    $this->mns->fake(xmlResponse());
    $this->mockedXml->expects()->decode(Mockery::any())->andReturn(['Subscription' => ['SubscriptionURL' => 'http://1234567891011.mns.cn-hangzhou.aliyuncs.com/topic-name/subscriptions/subscription-name']]);
    $result = $this->topic->listSubscriptionByTopic('topic-name', ['x-mns-prefix' => 'topic']);
    expect($result)->toBeInstanceOf(ListSubscriptionByTopicResult::class)
        ->and($result->subscriptions())->toBeList()
        ->and($result->subscriptions(0)->subscriptionUrl())->toBe('http://1234567891011.mns.cn-hangzhou.aliyuncs.com/topic-name/subscriptions/subscription-name');
});

test('publish message', function () {
    $this->mockedXml->expects()->encode(['Message' => ['MessageBody' => 'Hello world!']]);
    $result = $this->topic->publishMessage('topic-name', ['MessageBody' => 'Hello world!']);
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'POST' &&
        (string) $request->getUri() === $this->endpoint.'/topics/topic-name/messages'
    );
    expect($result)->toBeInstanceOf(PublishMessageResult::class);
});
