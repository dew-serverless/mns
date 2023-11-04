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
