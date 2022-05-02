<?php
declare(strict_types=1);

namespace Elephox\Support;

use BadMethodCallException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elephox\Support\TransparentProperties
 * @covers \Elephox\OOR\Casing
 *
 * @internal
 */
class TransparentPropertiesTest extends TestCase
{
	public function testGetter(): void
	{
		$obj = new ExamplePropertiesTestClass();
		$obj->value = 123;
		static::assertEquals(123, $obj->getValue());

		$this->expectException(BadMethodCallException::class);
		$this->expectExceptionMessage('Call to undefined method Elephox\Support\ExamplePropertiesTestClass::getNotExisting()');
		$obj->getNotExisting();
	}

	public function testSetter(): void
	{
		$obj = new ExamplePropertiesTestClass();
		$returned = $obj->setValue(123);
		static::assertEquals(123, $obj->value);
		static::assertSame($obj, $returned);

		$this->expectException(BadMethodCallException::class);
		$this->expectExceptionMessage('Call to undefined method Elephox\Support\ExamplePropertiesTestClass::setNotExisting()');
		$obj->setNotExisting(456);
	}

	public function testSetterRequiresArgument(): void
	{
		$obj = new ExamplePropertiesTestClass();
		$this->expectException(BadMethodCallException::class);
		$this->expectExceptionMessage('Elephox\Support\ExamplePropertiesTestClass::setValue() expects at least 1 argument');
		$obj->setValue();
	}
}

/**
 * @method int getValue()
 * @method self setValue(int $value)
 */
class ExamplePropertiesTestClass
{
	use TransparentProperties;

	public int $value = 1;
}
