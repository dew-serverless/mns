<?php

use Dew\Mns\Exceptions\XmlEncoderException;
use Dew\Mns\MnsXml;

test('could not encode empty data', function () {
    $xml = new MnsXml;
    expect(fn () => $xml->encode([]))->toThrow(XmlEncoderException::class, 'The data is empty.');
});

test('root element must be contain one', function () {
    $xml = new MnsXml;
    expect(fn () => $xml->encode(['root' => null, 'another-root' => null]))
        ->toThrow(XmlEncoderException::class, 'Document must contain only one root element.');
});

test('encode string data', function () {
    $content = (new MnsXml)->encode(['root' => 'foo']);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">foo</root>

EOF);
});

test('encode integer data', function () {
    $content = (new MnsXml)->encode(['root' => 123]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">123</root>

EOF);
});

test('encode float data', function () {
    $content = (new MnsXml)->encode(['root' => 3.14]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">3.14</root>

EOF);
});

test('encode bool data true', function () {
    $content = (new MnsXml)->encode(['root' => true]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">1</root>

EOF);
});

test('encode bool data false', function () {
    $content = (new MnsXml)->encode(['root' => false]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">0</root>

EOF);
});

test('encode null data', function () {
    $content = (new MnsXml)->encode(['root' => null]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"/>

EOF);
});

test('encode associative array data', function () {
    $content = (new MnsXml)->encode(['root' => ['greeting' => 'Hello world!']]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><greeting>Hello world!</greeting></root>

EOF);
});

test('encode associative array data sizes 2', function () {
    $content = (new MnsXml)->encode(['root' => ['item1' => 'foo', 'item2' => 'bar']]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><item1>foo</item1><item2>bar</item2></root>

EOF);
});

test('encode list array data', function () {
    $content = (new MnsXml)->encode(['root' => ['item' => [['name' => 'foo'], ['name' => 'bar']]]]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><item><name>foo</name></item><item><name>bar</name></item></root>

EOF);
});

test('encode list array data size 1', function () {
    $content = (new MnsXml)->encode(['root' => ['item' => [['name' => 'foo']]]]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><item><name>foo</name></item></root>

EOF);
});

test('encode list array default element name', function () {
    $content = (new MnsXml)->encode(['root' => [['name' => 'foo']]]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><item key="0"><name>foo</name></item></root>

EOF);
});

test('encode nested data', function () {
    $content = (new MnsXml)->encode(['root' => ['nested' => ['item' => 'foo']]]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><nested><item>foo</item></nested></root>

EOF);
});

test('encode mixed associative and list array data', function () {
    $content = (new MnsXml)->encode(['root' => ['mixed' => [['name' => 'foo'], 'name' => 'bar']]]);
    expect($content)->toBeString()->toBe(<<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><mixed><item key="0"><name>foo</name></item><name>bar</name></mixed></root>

EOF);
});

test('decode xml', function () {
    $document = <<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"><item>foo</item></root>
EOF;
    $result = (new MnsXml)->decode($document);
    expect($result)->toBe(['item' => 'foo']);
});

test('decode xml with nested data', function () {
    $document = <<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">
  <note><title>Note 1</title><body>foo</body></note>
</root>
EOF;
    $result = (new MnsXml)->decode($document);
    expect($result)->toBe(['note' => ['title' => 'Note 1', 'body' => 'foo']]);
});

test('decode xml with list data', function () {
    $document = <<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">
  <note><title>Note 1</title><body>foo</body></note>
  <note><title>Note 2</title><body>bar</body></note>
</root>
EOF;
    $result = (new MnsXml)->decode($document);
    expect($result)->toBe(['note' => [
        ['title' => 'Note 1', 'body' => 'foo'],
        ['title' => 'Note 2', 'body' => 'bar'],
    ]]);
});

test('decode scalar', function () {
    $document = <<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/">foo</root>
EOF;
    $result = (new MnsXml)->decode($document);
    expect($result)->toBe('foo');
});

test('could not decode with empty data', function () {
    $xml = new MnsXml;
    expect(fn () => $xml->decode(' '))->toThrow(XmlEncoderException::class, 'The data is empty.');
});

test('decode with only xml declaration', function () {
    $xml = new MnsXml;
    expect(fn () => $xml->decode('<?xml version="1.0"?>'))->toThrow(XmlEncoderException::class, "Start tag expected, '<' not found");
});

test('decode with empty root element', function () {
    $document = <<<'EOF'
<?xml version="1.0"?>
<root xmlns="http://mns.aliyuncs.com/doc/v1/"></root>
EOF;
    $result = (new MnsXml)->decode($document);
    expect($result)->toBe('');
});
