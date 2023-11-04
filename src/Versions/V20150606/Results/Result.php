<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606\Results;

use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\Versions\V20150606\Models\Model;
use Psr\Http\Message\ResponseInterface;

class Result extends Model
{
    /**
     * Create a result.
     */
    final public function __construct(
        protected ResponseInterface $response,
        protected XmlEncoder $xml
    ) {
        parent::__construct($this->decode());
    }

    /**
     * Decode the resopnse if necessarily.
     *
     * @return array<mixed>
     */
    private function decode(): array
    {
        if (! str_starts_with($this->response->getHeaderLine('content-type'), 'text/xml')) {
            return [];
        }

        $document = (string) $this->response->getBody();

        if ($document === '') {
            return [];
        }

        $decoded = $this->xml->decode($document);

        return is_string($decoded) ? [$decoded] : $decoded;
    }

    /**
     * Determine if the request was successful.
     */
    final public function successful(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    /**
     * Determine if the request was failed.
     */
    final public function failed(): bool
    {
        return $this->response->getStatusCode() >= 400;
    }

    /**
     * The request ID.
     */
    final public function requestId(): string
    {
        return $this->response->getHeaderLine('x-mns-request-id');
    }

    /**
     * The MNS version.
     */
    final public function mnsVersion(): string
    {
        return $this->response->getHeaderLine('x-mns-version');
    }

    /**
     * The error code.
     */
    final public function errorCode(): ?string
    {
        return $this->string('Code');
    }

    /**
     * The error description.
     */
    final public function errorMessage(): ?string
    {
        return $this->string('Message');
    }

    /**
     * Error reporting host.
     */
    final public function hostId(): ?string
    {
        return $this->string('HostId');
    }

    /**
     * Get the underlying response.
     */
    final public function toResponse(): ResponseInterface
    {
        return $this->response;
    }
}
