<?php
declare(strict_types=1);

namespace Elephox\Support;

use Elephox\Mimey\MimeType;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Elephox\Support\CustomMimeType
 *
 * @internal
 */
class CustomMimeTypeTest extends TestCase
{
	public function testInstantiate(): void
	{
		$mimeType = CustomMimeType::from('image/png', 'png');
		$builtIn = MimeType::ImagePng;

		static::assertInstanceOf(CustomMimeType::class, $mimeType);
		static::assertEquals($builtIn->getValue(), $mimeType->getValue());
		static::assertEquals($builtIn->getExtension(), $mimeType->getExtension());
	}

	public function testFromFilename(): void
	{
		$pngMimeType = CustomMimeType::fromFilename('test.png');

		static::assertEquals(MimeType::ImagePng, $pngMimeType);
		static::assertEquals('image/png', $pngMimeType->getValue());
		static::assertEquals('png', $pngMimeType->getExtension());

		$unknownMimeType = CustomMimeType::fromFilename('test.unknown');

		static::assertInstanceOf(CustomMimeType::class, $unknownMimeType);
		static::assertEquals('application/octet-stream', $unknownMimeType->getValue());
		static::assertEquals('unknown', $unknownMimeType->getExtension());
	}

	public function testFromFileInvalidType(): void
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Unable to determine mime type of file');

		CustomMimeType::fromFile(123);
	}

	public function testFromFileWithFilename(): void
	{
		$mimeType = CustomMimeType::fromFile('test.png');

		static::assertEquals(MimeType::ImagePng, $mimeType);
	}
}
