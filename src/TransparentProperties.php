<?php
declare(strict_types=1);

namespace Elephox\Support;

use BadMethodCallException;
use Elephox\OOR\Casing;

trait TransparentProperties
{
	/**
	 * @return iterable<int, string>
	 */
	protected function buildGetterPrefixes(): iterable
	{
		yield 'get';
		yield 'is';
		yield 'has';
		yield 'does';
	}

	/**
	 * @return iterable<int, string>
	 */
	protected function buildSetterPrefixes(): iterable
	{
		yield 'set';
		yield 'put';
		yield 'do';
	}

	/**
	 * @return iterable<int, string>
	 */
	protected function buildPropertyNames(string $name): iterable
	{
		yield $name;
		yield Casing::toCamel($name);
		yield Casing::toSnake($name);
		yield Casing::toPascal($name);
		yield 'has' . ucfirst($name);
		yield 'is' . ucfirst($name);
	}

	public function __call(string $name, array $arguments): mixed
	{
		$getProperty = null;

		foreach ($this->buildGetterPrefixes() as $prefix)
		{
			if (!str_starts_with($name, $prefix)) {
				continue;
			}

			$getProperty = lcfirst(substr($name, strlen($prefix)));

			break;
		}

		if ($getProperty !== null) {
			foreach ($this->buildPropertyNames($getProperty) as $propertyName) {
				if (property_exists($this, $propertyName)) {
					return $this->{$propertyName};
				}
			}
		}

		$setProperty = null;

		foreach ($this->buildSetterPrefixes() as $prefix)
		{
			if (!str_starts_with($name, $prefix)) {
				continue;
			}

			$setProperty = lcfirst(substr($name, strlen($prefix)));

			break;
		}

		if ($setProperty !== null) {
			foreach ($this->buildPropertyNames($setProperty) as $propertyName) {
				if (property_exists($this, $propertyName)) {
					if (count($arguments) === 0) {
						throw new BadMethodCallException(sprintf('%s::%s() expects at least 1 argument', static::class, $name));
					}

					$this->{$propertyName} = $arguments[0];

					return $this;
				}
			}
		}

		throw new BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
	}
}
