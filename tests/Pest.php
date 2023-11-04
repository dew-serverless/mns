<?php

use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\Versions\V20150606\Results\Result;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;

uses()->group('integration')->in('Integration');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Define semantic expectation is not only reduce the duplicated code but
| also improves the readability of test cases. Try finding the hidden
| pattern from complicated expectations and extract assertion here.
|
*/

expect()->extend('toBeList', function (string $message = '') {
    Assert::assertIsList($this->value, $message);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Helper functions are exposed to all of the test cases to let you easily
| access them to enhance code readability and maintainability. If your
| helpers growing large, you could define them in tests/Helpers.php.
|
*/

function xmlResponse(int $status = 200, array $headers = [], string $body = null)
{
    return new Response(
        $status,
        array_merge(['content-type' => 'text/xml'], $headers),
        $body ?: '<response></response>'
    );
}

function result(array $data, int $status = 200): Result
{
    $mockedXml = Mockery::mock(XmlEncoder::class);
    $mockedXml->expects()->decode(Mockery::any())->andReturn($data);

    return new Result(xmlResponse($status), $mockedXml);
}

function integrationTestEnabled(): bool
{
    $value = getenv('INTEGRATION_TEST_ENABLED');

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}
