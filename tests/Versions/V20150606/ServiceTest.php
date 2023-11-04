<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Results\OpenServiceResult;
use Dew\Mns\Versions\V20150606\Service;

beforeEach(function () {
    $this->mns = new MnsClient('http://1234567891011.mns.cn-hangzhou.aliyuncs.com', 'key', 'secret');
    $this->mns->fake();
    $this->service = new Service($this->mns);
});

test('open service', function () {
    $result = $this->service->openService();
    $this->mns->assertSent(fn ($request) => $request->getMethod() === 'POST' &&
        (string) $request->getUri() === 'http://1234567891011.mns.cn-hangzhou.aliyuncs.com/commonbuy/openservice'
    );
    expect($result)->toBeInstanceOf(OpenServiceResult::class);
});
