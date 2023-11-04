<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Tests\Fixtures\StubV20150606Api;

beforeEach(function () {
    $this->mns = new MnsClient('https://1234567891011.mns.cn-hangzhou.aliyuncs.com', 'key', 'secret');
    $this->mns->fake();
    $this->api = new StubV20150606Api($this->mns);
});

test('request has date header', function () {
    $this->api->testGet('/');
    $this->mns->assertSent(fn ($request) => $request->hasHeader('date') &&
        $request->getHeaderLine('date') === gmdate(DateTimeInterface::RFC7231)
    );
});

test('request has mns version', function () {
    $this->api->testGet('/');
    $this->mns->assertSent(fn ($request) => $request->hasHeader('x-mns-version') &&
        $request->getHeaderLine('x-mns-version') === '2015-06-06'
    );
});

test('post request content type is text/xml when data is array', function () {
    $this->api->testPost('/foo');
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/foo' && $request->hasHeader('content-type') === false);

    $this->api->testPost('/bar', data: ['foo' => 'bar']);
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/bar' && $request->hasHeader('content-type') && $request->getHeaderLine('content-type') === 'text/xml');
});

test('put request content type is text/xml when data is array', function () {
    $this->api->testPut('/foo');
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/foo' && $request->hasHeader('content-type') === false);

    $this->api->testPut('/bar', data: ['foo' => 'bar']);
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/bar' && $request->hasHeader('content-type') && $request->getHeaderLine('content-type') === 'text/xml');
});

test('delete request content type is text/xml when data is array', function () {
    $this->api->testDelete('/foo');
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/foo' && $request->hasHeader('content-type') === false);

    $this->api->testDelete('/bar', data: ['foo' => 'bar']);
    $this->mns->assertSent(fn ($request) => $request->getUri()->getPath() === '/bar' && $request->hasHeader('content-type') && $request->getHeaderLine('content-type') === 'text/xml');
});

test('api is extendable', function () {
    StubV20150606Api::extend('newApi', fn () => 'called');
    expect($this->api->newApi())->toBe('called');
});
