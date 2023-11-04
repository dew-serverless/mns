<?php

namespace Dew\Mns\Tests\Fixtures;

use Dew\Mns\Versions\V20150606\Api;
use Psr\Http\Message\ResponseInterface;

class StubV20150606Api extends Api
{
    public function testGet(string $uri, array $headers = [], array $data = []): ResponseInterface
    {
        return $this->get($uri, $headers, $data);
    }

    public function testPost(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        return $this->post($uri, $headers, $data);
    }

    public function testPut(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        return $this->put($uri, $headers, $data);
    }

    public function testDelete(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        return $this->delete($uri, $headers, $data);
    }
}
