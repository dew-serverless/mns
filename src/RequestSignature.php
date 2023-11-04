<?php

declare(strict_types=1);

namespace Dew\Mns;

use Dew\Mns\Contracts\BuildsSignature;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

final class RequestSignature implements BuildsSignature
{
    /**
     * Create a new signer.
     */
    public function __construct(protected string $key, protected string $algo = 'sha1')
    {
        if (! in_array($this->algo, hash_hmac_algos(), strict: true)) {
            throw new RuntimeException(sprintf('Unsupported signature algorithm [%s].', $this->algo));
        }
    }

    /**
     * Build signature for the given request.
     */
    public function build(RequestInterface $request): string
    {
        return base64_encode(hash_hmac(
            $this->algo, $this->data($request), $this->key, binary: true
        ));
    }

    /**
     * The data extracted from the given request.
     */
    public function data(RequestInterface $request): string
    {
        return implode("\n", [
            $request->getMethod(),
            $request->getHeaderLine('content-md5'),
            $request->getHeaderLine('content-type'),
            $this->getDate($request),
            $this->canonicalizedHeaders($request),
            $this->canonicalizedResource($request),
        ]);
    }

    /**
     * Get the date represent the given request.
     */
    public function getDate(RequestInterface $request): string
    {
        $date = $request->getHeaderLine('date');

        return $date === '' ? $request->getHeaderLine('x-mns-date') : $date;
    }

    /**
     * Build canonicalized headers from the given request.
     */
    public function canonicalizedHeaders(RequestInterface $request): string
    {
        $headers = array_change_key_case($request->getHeaders(), CASE_LOWER);
        ksort($headers);

        $canonicalized = [];

        foreach ($headers as $name => $values) {
            if ($this->isMnsHeader($name)) {
                $canonicalized[] = $name.':'.$values[0];
            }
        }

        return implode("\n", $canonicalized);
    }

    /**
     * Build canonicalized resource from the given request.
     */
    public function canonicalizedResource(RequestInterface $request): string
    {
        $path = $request->getUri()->getPath();

        $queryString = $request->getUri()->getQuery();

        if ($queryString === '') {
            return $path;
        }

        return $path.'?'.$queryString;
    }

    /**
     * Determine if the header name is a MNS custom header.
     */
    private function isMnsHeader(string $header): bool
    {
        return str_starts_with(strtolower($header), 'x-mns-');
    }

    /**
     * The encryption key.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * The hashing algorithm.
     */
    public function algorithm(): string
    {
        return $this->algo;
    }
}
