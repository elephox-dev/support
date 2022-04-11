<?php
declare(strict_types=1);

namespace Elephox\Support;

use BadMethodCallException;
use Elephox\OOR\Casing;

trait TransparentGetterSetter
{
	protected function buildGetterName(string $propertyName): string
	{
		return 'get' . Casing::toPascal($propertyName);
	}

	protected function buildSetterName(string $propertyName): string
	{
		return 'set' . Casing::toPascal($propertyName);
	}

	public function __get(string $name)
	{
		$method = $this->buildGetterName($name);
		if (method_exists($this, $method)) {
			return $this->$method();
		}

		throw new BadMethodCallException('Method ' . $method . ' does not exist.');
	}

	public function __set(string $name, $value)
	{
		$method = $this->buildSetterName($name);
		if (method_exists($this, $method)) {
			return $this->$method($value);
		}

		throw new BadMethodCallException('Method ' . $method . ' does not exist.');
	}

	public function __isset(string $name)
	{
		$method = $this->buildGetterName($name);

		return method_exists($this, $method);
	}
}