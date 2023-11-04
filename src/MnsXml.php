<?php

declare(strict_types=1);

namespace Dew\Mns;

use Dew\Mns\Contracts\XmlEncoder;
use Dew\Mns\Exceptions\XmlEncoderException;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use LibXMLError;

final class MnsXml implements XmlEncoder
{
    /**
     * Encode data with XML document.
     *
     * @param  array<mixed>  $data
     */
    public function encode(array $data): string
    {
        if ($data === []) {
            throw new XmlEncoderException('The data is empty.');
        }

        if (count($data) > 1) {
            throw new XmlEncoderException('Document must contain only one root element.');
        }

        $document = new DOMDocument;

        $rootName = array_key_first($data);

        $data = $data[$rootName];

        if (is_array($data)) {
            $root = $document->createElement($rootName);
            $document->appendChild($root);
            $this->buildArray($root, $data);
        } elseif ($data === null || is_scalar($data)) {
            $this->appendNode($document, $data, $rootName);
        }

        $document->firstElementChild?->setAttribute('xmlns', 'http://mns.aliyuncs.com/doc/v1/');

        $xml = $document->saveXML();

        return $xml === false ? '' : $xml;
    }

    /**
     * Decode data encoded with XML document.
     *
     * @return array<mixed>|string
     */
    public function decode(string $data): array|string
    {
        if (trim($data) === '') {
            throw new XmlEncoderException('The data is empty.');
        }

        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);

        $document->loadXML($data, LIBXML_NOENT | LIBXML_NOBLANKS);

        libxml_use_internal_errors($previous);

        $error = libxml_get_last_error();

        if ($error instanceof LibXMLError) {
            libxml_clear_errors();

            throw new XmlEncoderException($error->message);
        }

        foreach ($document->childNodes as $node) {
            return $this->decodeNodeValue($node);
        }

        return '';
    }

    /**
     * Build nodes from the given array.
     *
     * @param  array<mixed>  $data
     */
    private function buildArray(DOMNode $node, array $data): void
    {
        foreach ($data as $key => $value) {
            if (! is_numeric($key) && is_array($value) && array_is_list($value)) {
                foreach ($value as $item) {
                    $this->appendNode($node, $item, $key);
                }
            } elseif (is_numeric($key)) {
                $this->appendNode($node, $value, 'item', $key);
            } else {
                $this->appendNode($node, $value, $key);
            }
        }
    }

    /**
     * Append the DOM element to the given node.
     */
    private function appendNode(DOMNode $node, mixed $data, string $nodeName, int $key = null): void
    {
        $document = $node instanceof DOMDocument ? $node : $node->ownerDocument;

        $element = $document->createElement($nodeName);

        if ($key !== null) {
            $element->setAttribute('key', (string) $key);
        }

        if (is_array($data)) {
            $this->buildArray($element, $data);
        } elseif (is_string($data)) {
            $this->appendText($element, $data);
        } elseif (is_bool($data)) {
            $this->appendText($element, (string) (int) $data);
        } elseif (is_numeric($data)) {
            $this->appendText($element, (string) $data);
        }

        $node->appendChild($element);
    }

    /**
     * Append string data to the given node.
     */
    private function appendText(DOMNode $node, string $data): void
    {
        $node->appendChild(
            $node->ownerDocument->createTextNode($data)
        );
    }

    /**
     * Decode the value from the given node list.
     *
     * @param  DOMNodeList<DOMNode>  $nodes
     * @return array<mixed>
     */
    private function decodeNodes(DOMNodeList $nodes): array
    {
        $data = [];

        foreach ($nodes as $node) {
            $data[$node->nodeName][] = $this->decodeNodeValue($node);
        }

        foreach ($data as $key => $value) {
            if (count($value) === 1) {
                $data[$key] = $value[0];
            }
        }

        return $data;
    }

    /**
     * Decode the value of the node.
     *
     * @return array<string,mixed>|string
     */
    private function decodeNodeValue(DOMNode $node): array|string
    {
        if (! $node->hasChildNodes()) {
            return (string) $node->nodeValue;
        }

        if ($node->firstChild?->nodeType === XML_TEXT_NODE) {
            return (string) $node->firstChild->nodeValue;
        }

        return $this->decodeNodes($node->childNodes);
    }
}
