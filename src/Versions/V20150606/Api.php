<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606;

use BadMethodCallException;
use DateTimeInterface;
use Dew\Mns\Concerns\Extendable;
use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\MnsClient;
use Dew\Mns\MnsXml;
use Psr\Http\Message\ResponseInterface;

abstract class Api
{
    use Extendable;

    /**
     * The XML encoder.
     */
    protected XmlEncoder $xml;

    /**
     * Create a V20150606 API.
     */
    public function __construct(
        protected MnsClient $client
    ) {
        //
    }

    /**
     * The XML encoder.
     */
    final public function xml(): XmlEncoder
    {
        return $this->xml ??= new MnsXml;
    }

    /**
     * Set XML encoder.
     */
    final public function xmlUsing(XmlEncoder $xml): self
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Send a GET request.
     *
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $data
     */
    final protected function get(string $uri, array $headers = [], array $data = []): ResponseInterface
    {
        return $this->client->get($uri, $this->buildHeaders($headers), $data);
    }

    /**
     * Send a POST request.
     *
     * @param  array<string, string>  $headers
     * @param  array<mixed>|string|null  $data
     */
    final protected function post(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        if (is_array($data)) {
            $data = $this->xml()->encode($data);

            $headers['content-type'] = 'text/xml';
        }

        return $this->client->post($uri, $this->buildHeaders($headers), $data);
    }

    /**
     * Send a PUT request.
     *
     * @param  array<string, string>  $headers
     * @param  array<mixed>|string|null  $data
     */
    final protected function put(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        if (is_array($data)) {
            $data = $this->xml()->encode($data);

            $headers['content-type'] = 'text/xml';
        }

        return $this->client->put($uri, $this->buildHeaders($headers), $data);
    }

    /**
     * Send a DELETE request.
     *
     * @param  array<string, string>  $headers
     * @param  array<mixed>|string|null  $data
     */
    final protected function delete(string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        if (is_array($data)) {
            $data = $this->xml()->encode($data);

            $headers['content-type'] = 'text/xml';
        }

        return $this->client->delete($uri, $this->buildHeaders($headers), $data);
    }

    /**
     * Build the headers with default one.
     *
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    final protected function buildHeaders(array $headers = []): array
    {
        return [...$this->defaultHeaders(), ...$headers];
    }

    /**
     * The default headers attached to every requests.
     *
     * @return array<string, string>
     */
    final protected function defaultHeaders(): array
    {
        return [
            'date' => gmdate(DateTimeInterface::RFC7231),
            'x-mns-version' => '2015-06-06',
        ];
    }

    /**
     * The underlying MNS client.
     */
    final public function client(): MnsClient
    {
        return $this->client;
    }

    /**
     * Handle inaccessable methods.
     *
     * @param  array<int, mixed>  $parameters
     */
    final public function __call(string $method, array $parameters): mixed
    {
        if (static::hasExtend($method)) {
            return static::callExtend($method, $parameters);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $method));
    }
}
