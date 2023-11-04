<?php

test('check if result is successful', function () {
    expect(result([], 200)->successful())->toBeTrue();
    expect(result([], 204)->successful())->toBeTrue();
    expect(result([], 400)->successful())->toBeFalse();
    expect(result([], 404)->successful())->toBeFalse();
    expect(result([], 500)->successful())->toBeFalse();
});

test('check if result is failed', function () {
    expect(result([], 200)->successful())->toBeTrue();
    expect(result([], 204)->successful())->toBeTrue();
    expect(result([], 400)->successful())->toBeFalse();
    expect(result([], 404)->successful())->toBeFalse();
    expect(result([], 500)->successful())->toBeFalse();
});
