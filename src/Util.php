<?php

namespace Invoiced;

class Util
{
    /**
     * Converts values into an Invoiced object.
     *
     * @param object $class
     * @param array  $values
     *
     * @return object
     */
    public static function convertToObject($class, array $values)
    {
        $className = get_class($class);

        $object = new $className($class->getClient(), $values['id'], $values);
        $object->setEndpointBase($class->getEndpointBase());

        return $object;
    }

    /**
     * Converts values into an Invoiced object; forces `id` to placeholder `-1`.
     *
     * @param object $class
     * @param array  $values
     *
     * @return object
     */
    public static function convertPreviewToObject($class, array $values)
    {
        $className = get_class($class);

        $object = new $className($class->getClient(), -1, $values);
        $object->setEndpointBase($class->getEndpointBase());

        return $object;
    }

    /**
     * Converts a list of object values into object classes.
     *
     * @param object|string $class
     * @param array         $objects
     *
     * @return array array(Object)
     */
    public static function buildObjects($class, array $objects)
    {
        return array_map(function ($object) use ($class) {
            return self::convertToObject($class, $object);
        }, $objects);
    }
}
