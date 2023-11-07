<?php

declare(strict_types=1);

namespace Dew\Mns;

use Closure;
use Dew\Mns\Contracts\BuildsSignature;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MnsClient
{
    /**
     * The request options.
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * The signature builder.
     */
    private BuildsSignature $signature;

    /**
     * Determine if the requests are being recorded.
     */
    private bool $recording = false;

    /**
     * The recorded requests.
     *
     * @var RequestInterface[]
     */
    private array $recorded = [];

    /**
     * The stub response.
     */
    private ResponseInterface $stubResponse;

    /**
     * Create a MNS client.
     */
    public function __construct(
        protected string $endpoint,
        protected string $accessKeyId,
        protected string $accessKeySecret
    ) {
        //
    }

    /**
     * The MNS endpoint.
     */
    public function endpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * The ACS access key ID.
     */
    public function accessKeyId(): string
    {
        return $this->accessKeyId;
    }

    /**
     * The ACS access key secret.
     */
    public function accessKeySecret(): string
    {
        return $this->accessKeySecret;
    }

    /**
     * Configure request options.
     *
     * @param  array<string, mixed>  $config
     */
    public function configure(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * The signature builder.
     */
    public function signature(): BuildsSignature
    {
        return $this->signature ??= new RequestSignature($this->accessKeySecret);
    }

    /**
     * Set signature builder.
     */
    public function signatureUsing(BuildsSignature $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Configure client into faking mode.
     */
    public function fake(ResponseInterface $stub = null): self
    {
        $this->record();

        $this->stubResponse = $stub ?? new Response;

        return $this;
    }

    /*
     * Start to record requests.
     */
    private function record(): self
    {
        $this->recording = true;

        return $this;
    }

    /**
     * Assert the given request had been sent.
     *
     * @param  Closure(RequestInterface): bool  $callback
     */
    public function assertSent(callable $callback): void
    {
        Assert::assertNotEmpty(
            array_filter($this->recorded, $callback),
            'The expected request was not being sent.'
        );
    }

    /**
     * Send a GET request.
     *
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $data
     */
    public function get(string $uri, array $headers = [], array $data = null): ResponseInterface
    {
        return $this->send('GET', $uri, $headers, $data);
    }

    /**
     * Send a POST request.
     *
     * @param  array<string, string>  $headers
     */
    public function post(string $uri, array $headers = [], string $data = null): ResponseInterface
    {
        return $this->send('POST', $uri, $headers, $data);
    }

    /**
     * Send a PUT request.
     *
     * @param  array<string, string>  $headers
     */
    public function put(string $uri, array $headers = [], string $data = null): ResponseInterface
    {
        return $this->send('PUT', $uri, $headers, $data);
    }

    /**
     * Send a DELETE request.
     *
     * @param  array<string, string>  $headers
     */
    public function delete(string $uri, array $headers = [], string $data = null): ResponseInterface
    {
        return $this->send('DELETE', $uri, $headers, $data);
    }

    /**
     * Send a HTTP request.
     *
     * @param  array<string, string>  $headers
     * @param  array<mixed>|string|null  $data
     */
    private function send(string $method, string $uri, array $headers = [], array|string $data = null): ResponseInterface
    {
        try {
            return $this->client()->request($method, $uri, [
                'headers' => $headers,
                ($method === 'GET' ? 'query' : 'body') => $data,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if (! $response instanceof ResponseInterface) {
                throw $e;
            }

            return $response;
        }
    }

    /**
     * Make a new Guzzle client.
     */
    private function client(): Client
    {
        return new Client([...$this->getConfiguration(), ...[
            'base_uri' => $this->endpoint,
            'handler' => $this->createHandler(),
        ]]);
    }

    /**
     * Get configuration for Guzzle client.
     *
     * @return array<string, mixed>
     */
    private function getConfiguration(): array
    {
        $default = [
            'timeout' => 60.0,
        ];

        return [...$default, ...$this->config];
    }

    /**
     * Create handler stack for client.
     */
    private function createHandler(): HandlerStack
    {
        $handler = HandlerStack::create();

        $handler->push($this->signatureMiddleware());

        if ($this->recording) {
            $handler->push($this->recorderMiddleware());
            $handler->push($this->stubResponseMiddleware());
        }

        return $handler;
    }

    /**
     * The middleware to calculate the signature of the request.
     *
     * @return Closure(callable $handler): Closure(RequestInterface $request, array)
     */
    private function signatureMiddleware(): Closure
    {
        return fn (callable $handler): Closure => function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader('Authorization', sprintf('MNS %s:%s',
                $this->accessKeyId, $this->signature()->build($request)
            ));

            return $handler($request, $options);
        };
    }

    /**
     * The middleware to record the requests.
     *
     * @return Closure(callable $handler): Closure(RequestInterface, array)
     */
    private function recorderMiddleware(): Closure
    {
        return fn (callable $handler): Closure => function (RequestInterface $request, array $options) use ($handler) {
            $this->recorded[] = $request;

            return $handler($request, $options);
        };
    }

    /**
     * The middleware to return stub response in faking mode.
     *
     * @return Closure(callable $handler): Closure(RequestInterface, array)
     */
    private function stubResponseMiddleware(): Closure
    {
        return fn (callable $handler): callable => fn (RequestInterface $request, array $options): PromiseInterface => Promise\Create::promiseFor($this->stubResponse);
    }
}
