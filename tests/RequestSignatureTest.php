<?php

use Dew\Mns\RequestSignature;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

test('signature calculation', function () {
    $mockedUri = Mockery::mock(UriInterface::class);
    $mockedUri->shouldReceive('getPath')->once()->andReturn('/queues/queue-name');
    $mockedUri->shouldReceive('getQuery')->once()->andReturn('');
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getMethod')->once()->andReturn('PUT');
    $mockedRequest->shouldReceive('getHeaderLine')->with('content-md5')->once()->andReturn('');
    $mockedRequest->shouldReceive('getHeaderLine')->with('content-type')->once()->andReturn('text/xml');
    $mockedRequest->shouldReceive('getHeaderLine')->with('date')->once()->andReturn('Wed, 08 Mar 2012 12:00:00 GMT');
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([]);
    $mockedRequest->shouldReceive('getUri')->andReturn($mockedUri);
    $signature = new RequestSignature('key');
    expect($signature->build($mockedRequest))->toBeString()->not->toBeEmpty();
});

test('date with x-mns-date header', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaderLine')->with('date')->once()->andReturn('');
    $mockedRequest->shouldReceive('getHeaderLine')->with('x-mns-date')->once()->andReturn('Wed, 08 Mar 2012 12:00:00 GMT');
    $signature = new RequestSignature('key');
    expect($signature->getDate($mockedRequest))->toBe('Wed, 08 Mar 2012 12:00:00 GMT');
});

test('canonicalized headers are mns custom headers', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([
        'Content-Type' => ['text/xml'],
        'x-mns-request-id' => ['512B2A634403E52B1956****'],
    ]);
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedHeaders($mockedRequest))->toBe('x-mns-request-id:512B2A634403E52B1956****');
});

test('canonicalized header should be sorted ascendingly by name', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([
        'x-mns-def' => ['bar'],
        'x-mns-abc' => ['foo'],
    ]);
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedHeaders($mockedRequest))->toBe("x-mns-abc:foo\nx-mns-def:bar");
});

test('canonicalized header name are lower case', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([
        'X-MNS-REQUEST-ID' => ['512B2A634403E52B1956****'],
    ]);
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedHeaders($mockedRequest))->toBe('x-mns-request-id:512B2A634403E52B1956****');
});

test('canonicalized headers are empty when no mns headers', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([
        'Content-Type' => ['text/xml'],
    ]);
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedHeaders($mockedRequest))->toBe('');
});

test('canonicalized headers are empty when no headers', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getHeaders')->once()->andReturn([]);
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedHeaders($mockedRequest))->toBe('');
});

test('builds canonicalized response', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getUri->getPath')->once()->andReturn('/queues/queue-name');
    $mockedRequest->shouldReceive('getUri->getQuery')->once()->andReturn('');
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedResource($mockedRequest))->toBe('/queues/queue-name');
});

test('builds canonicalized response with query string', function () {
    $mockedRequest = Mockery::mock(RequestInterface::class);
    $mockedRequest->shouldReceive('getUri->getPath')->once()->andReturn('/queues/queue-name');
    $mockedRequest->shouldReceive('getUri->getQuery')->once()->andReturn('foo=bar');
    $signature = new RequestSignature('key');
    expect($signature->canonicalizedResource($mockedRequest))->toBe('/queues/queue-name?foo=bar');
});

test('handles unsupported sign algorithm', function () {
    expect(fn () => new RequestSignature('key', 'foo'))
        ->toThrow(RuntimeException::class, 'Unsupported signature algorithm [foo].');
});
