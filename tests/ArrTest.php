<?php

use Dew\Mns\Support\Arr;

test('list wraps in list', function () {
    expect(Arr::list(['name' => 'Zhineng']))->toBe([['name' => 'Zhineng']]);
    expect(Arr::list([['name' => 'Zhineng']]))->toBe([['name' => 'Zhineng']]);
    expect(Arr::list([]))->toBe([]);
});

test('set array', function () {
    $result = [];
    Arr::set($result, 'foo', 'bar');
    expect($result)->toBe(['foo' => 'bar']);
});

test('set array with dot notation', function () {
    $result = ['nested' => ['foo' => null]];
    Arr::set($result, 'nested.foo', 'bar');
    expect($result)->toBe(['nested' => ['foo' => 'bar']]);
});

test('set array makes not exists key', function () {
    $result = [];
    Arr::set($result, 'nested.foo', 'bar');
    expect($result)->toBe(['nested' => ['foo' => 'bar']]);
});

test('set array converts value into array if necessarily', function () {
    $result = ['value' => 'foo'];
    Arr::set($result, 'value.foo', 'bar');
    expect($result)->toBe(['value' => ['foo' => 'bar']]);
});

test('has determines key existence', function () {
    $result = ['value' => 'foo'];
    expect(Arr::has($result, 'value'))->toBeTrue();
    expect(Arr::has($result, 'not-exists'))->toBeFalse();
});

test('has supports dot notation', function () {
    $result = ['nested' => ['value' => 'foo']];
    expect(Arr::has($result, 'nested.value'))->toBeTrue();
    expect(Arr::has($result, 'nested.not-exists'))->toBeFalse();
});

test('has determines with dotted character', function () {
    $result = ['nested.value' => 'foo'];
    expect(Arr::has($result, 'nested.value'))->toBeTrue();
});

test('has determines with index key', function () {
    $result = ['value' => ['foo', 'bar']];
    expect(Arr::has($result, 'value.0'))->toBeTrue();
    expect(Arr::has($result, 'value.1'))->toBeTrue();
    expect(Arr::has($result, 'value.2'))->toBeFalse();
});

test('get array', function () {
    $result = Arr::get(['foo' => 'bar'], 'foo');
    expect($result)->toBe('bar');
});

test('get array with dot notation', function () {
    $result = Arr::get(['nested' => ['foo' => 'bar']], 'nested.foo');
    expect($result)->toBe('bar');
});

test('get array with not exists key', function () {
    expect(Arr::get([], 'foo'))->toBeNull();
});

test('get array with default value', function () {
    expect(Arr::get([], 'foo', 'default'))->toBe('default');
});

test('get array with null key returns original array', function () {
    expect(Arr::get(['value' => 'foo'], null))->toBe(['value' => 'foo']);
});

test('get array with index key', function () {
    $array = ['value' => ['foo', 'bar']];
    expect(Arr::get($array, 'value.0'))->toBe('foo');
    expect(Arr::get($array, 'value.1'))->toBe('bar');
    expect(Arr::get($array, 'value.2'))->toBeNull();
});

test('get array with array key contains dot string', function () {
    $array = ['value.foo' => 'bar'];
    expect(Arr::get($array, 'value.foo'))->toBe('bar');
});

test('get array prioritizes dotted array key', function () {
    $array = ['value.foo' => 'bar', 'value' => ['foo' => 'not-this-one']];
    expect(Arr::get($array, 'value.foo'))->toBe('bar');
});
