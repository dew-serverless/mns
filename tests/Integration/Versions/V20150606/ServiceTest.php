<?php

use Dew\Mns\MnsClient;
use Dew\Mns\Versions\V20150606\Service;

beforeEach(function () {
    $this->mns = new MnsClient(getenv('MNS_ENDPOINT'), getenv('ACS_ACCESS_KEY_ID'), getenv('ACS_ACCESS_KEY_SECRET'));
    $this->service = new Service($this->mns);
})->skip(! integrationTestEnabled(), 'Integration test is not enabled.');

test('open service', function () {
    $result = $this->service->openService();
    expect($result->successful())->toBeTrue()
        ->and($result->orderId())->toBeString()->not->toBeEmpty();
})->skip();
