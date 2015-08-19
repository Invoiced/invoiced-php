<?php

namespace Invoiced;

class Util
{
	static function convertToObject($class, array $object)
	{
		$_class = get_class($class);

		return new $_class($class->getClient(), $object['id'], $object);
	}

	static function buildObjects($class, $objects)
	{
		return array_map(function($object) use ($class) {
			return self::convertToObject($class, $object);
		}, $objects);
	}
}