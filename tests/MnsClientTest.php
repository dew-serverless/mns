<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Tests\Fixtures\StubSignature;

test('request has authorization header', function () {
    $mns = new MnsClient('https://123456789101112.mns.cn-hangzhou.aliyuncs.com', 'key', 'secret');
    $mns->signatureUsing(new StubSignature('foo'));
    $mns->fake();
    $mns->get('/');
    $mns->assertSent(fn ($request) => $request->hasHeader('Authorization') && $request->getHeaderLine('Authorization') === 'MNS key:foo');
});

test('configures request options', function () {
    $mns = new MnsClient('https://123456789101112.mns.cn-hangzhou.aliyuncs.com', 'key', 'secret');
    $mns->configure(['headers' => ['X-Foo' => 'Bar']]);
    $mns->fake();
    $mns->get('/');
    $mns->assertSent(fn ($request) => $request->getHeaderLine('X-Foo') === 'Bar');
});
