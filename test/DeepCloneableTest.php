<?php
declare(strict_types=1);

namespace Elephox\Support;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use WeakMap;

/**
 * @covers \Elephox\Support\DeepCloneable
 */
class DeepCloneTest extends TestCase
{
	public function testThrowsRuntimeException(): void
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Cloning of ' . Cloneable::class . ' failed.');

		$object = new Cloneable();
		$object->throwOnClone = new ThrowOnClone();
		$object->deepClone();
	}

	public function testCloneResourceStaysSame(): void
	{
		$resource = fopen('php://memory', 'rb');

		$object = new Cloneable();
		$object->resource = $resource;
		$object->deepClone();

		self::assertSame($resource, $object->resource);

		fclose($object->resource);
	}

	public function testEnumMembersStaySame(): void
	{
		$object = new Cloneable();
		$object->enumValue = TestEnum::A;

		$clone = $object->deepClone();

		self::assertSame(TestEnum::A, $clone->enumValue);
	}

	public function testStaticPropertyDoesntChange(): void
	{
		$object = new Cloneable();
		$o1 = new stdClass();
		$o2 = new stdClass();
		Cloneable::$staticProperty = $o1;
		Cloneable::$anotherStaticProperty = $o2;

		$object->deepClone();

		self::assertSame($o1, Cloneable::$staticProperty);
		self::assertSame($o2, Cloneable::$anotherStaticProperty);
	}

	public function testWeakMapKeysAreKept(): void
	{
		$object = new HasWeakMap();
		$o1 = new stdClass();
		$o2 = new stdClass();
		$object->weakMap = new WeakMap();
		$object->weakMap->offsetSet($o1, $o2);

		$clone = $object->deepClone();

		self::assertTrue($clone->weakMap->offsetExists($o1));
		self::assertNotSame($o2, $clone->weakMap->offsetGet($o1));
	}
}

class Cloneable
{
	use DeepCloneable;

	public ?ThrowOnClone $throwOnClone = null;
	public static $staticProperty;

	public $resource;

	public static $anotherStaticProperty;

	public ?TestEnum $enumValue = null;
}

class ThrowOnClone
{
	public function __clone()
	{
		throw new RuntimeException('Cloning not allowed');
	}
}

class HasWeakMap
{
	use DeepCloneable;

	public WeakMap $weakMap;
}

enum TestEnum {
	case A;
	case B;
}