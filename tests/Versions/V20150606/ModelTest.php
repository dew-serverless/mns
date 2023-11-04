<?php

use Dew\Mns\Tests\Fixtures\StubV20150606Model;

test('get value', function () {
    $model = new StubV20150606Model(['value' => 'foo']);
    expect($model->get('value'))->toBe('foo');
});

test('get value with not exists data', function () {
    $model = new StubV20150606Model([]);
    expect($model->get('value'))->toBeNull();
    expect($model->bool('value'))->toBeNull();
    expect($model->int('value'))->toBeNull();
    expect($model->timestamp('value'))->toBeNull();
    expect($model->timestampMs('value'))->toBeNull();
});

test('get value with default value', function () {
    $model = new StubV20150606Model([]);
    expect($model->get('value', 'default'))->toBe('default');
});

test('get bool value', function () {
    $model = new StubV20150606Model(['true' => true, 'false' => false]);
    expect($model->get('true'))->toBe(true)
        ->and($model->get('false'))->toBe(false)
        ->and($model->bool('true'))->toBe(true)
        ->and($model->bool('false'))->toBe(false);
});

test('get bool value with boolean string', function () {
    $model = new StubV20150606Model(['true' => 'True', 'false' => 'False']);
    expect($model->get('true'))->toBe('True')
        ->and($model->get('false'))->toBe('False')
        ->and($model->bool('true'))->toBe(true)
        ->and($model->bool('false'))->toBe(false);
});

test('get bool value with deafult value', function () {
    $model = new StubV20150606Model([]);
    expect($model->bool('true', true))->toBe(true)
        ->and($model->bool('false', false))->toBe(false);
});

test('get integer value', function () {
    $model = new StubV20150606Model(['value' => '12345']);
    expect($model->get('value'))->toBe('12345')
        ->and($model->int('value'))->toBe(12345);
});

test('get integer value with default value', function () {
    $model = new StubV20150606Model([]);
    expect($model->int('value', 12345))->toBe(12345);
});

test('get timestamp value', function () {
    $model = new StubV20150606Model(['value' => '1698810814']);
    expect($model->get('value'))->toBe('1698810814')
        ->and($model->timestamp('value'))->toBeInstanceOf(DateTimeInterface::class)
        ->and($model->timestamp('value')->getTimestamp())->toBe(1698810814);
});

test('get timestamp value in millisecond', function () {
    $model = new StubV20150606Model(['value' => '1698810814145']);
    expect($model->get('value'))->toBe('1698810814145')
        ->and($model->timestampMs('value'))->toBeInstanceOf(DateTimeInterface::class)
        ->and($model->timestampMs('value')->format('U.u'))->toBe('1698810814.145000');
});

test('get timestamp value in millisecond with invalid timestamp', function () {
    $model = new StubV20150606Model(['value' => 'foo']);
    expect($model->timestampMs('value'))->toBeNull();
});

test('get timestamp value in millisecond with only millisecond given', function () {
    $model = new StubV20150606Model(['value' => '145']);
    expect($model->timestampMs('value'))->toBeInstanceOf(DateTimeInterface::class)
        ->and($model->timestampMs('value')->format('U.u'))->toBe('0.145000');
});

test('get timestamp value in millisecond with empty string', function () {
    $model = new StubV20150606Model(['value' => '']);
    expect($model->timestampMs('value'))->toBeInstanceOf(DateTimeInterface::class)
        ->and($model->timestampMs('value')->format('U.u'))->toBe('0.000000');
});

test('get timestamp value in millisecond with default value', function () {
    $model = new StubV20150606Model(['value' => null]);
    expect($model->timestampMs('value', $now = new DateTimeImmutable('now')))->toBe($now);
});

test('array access get', function () {
    $model = new StubV20150606Model(['name' => 'Zhineng']);
    expect($model['name'])->toBe('Zhineng');
});

test('array access get with dot notation', function () {
    $model = new StubV20150606Model(['person' => ['name' => 'Zhineng']]);
    expect($model['person.name'])->toBe('Zhineng');
});

test('array access isset', function () {
    $model = new StubV20150606Model(['name' => 'Zhineng']);
    expect(isset($model['name']))->toBeTrue();
    expect(isset($model['email']))->toBeFalse();
});

test('array access isset with dot notation', function () {
    $model = new StubV20150606Model(['person' => ['name' => 'Zhineng']]);
    expect(isset($model['person.name']))->toBeTrue();
    expect(isset($model['person.email']))->toBeFalse();
});

test('could not mutate the data through array syntax', function () {
    $model = new StubV20150606Model(['name' => 'Zhineng']);

    expect(function () use ($model) {
        $model['name'] = 'Shiyun';
    })->toThrow(LogicException::class, 'Could not mutate the model data.');

    expect(function () use ($model) {
        unset($model['name']);
    })->toThrow(LogicException::class, 'Could not mutate the model data.');
});

test('model is extendable', function () {
    StubV20150606Model::extend('somethingNew', fn () => 'foo');
    $model = new StubV20150606Model([]);
    expect($model->somethingNew())->toBe('foo');
});
