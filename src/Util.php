<?php

namespace Invoiced;

class Util
{
    /**
     * Converts values into an Invoiced object.
     *
     * @param BaseObject   $class
     * @param array<mixed> $values
     *
     * @return BaseObject
     */
    public static function convertToObject($class, array $values)
    {
        $className = get_class($class);

        if (isset($values['object'])) {
            if ('card' == $values['object']) {
                $className = 'Invoiced\\Card';
            } elseif ('bank_account' == $values['object']) {
                $className = 'Invoiced\\BankAccount';
            }
        }

        $object = new $className($class->getClient(), $values['id'], $values);
        $object->setEndpointBase($class->getEndpointBase());

        return $object;
    }

    /**
     * Converts a list of object values into object classes.
     *
     * @param BaseObject   $class
     * @param array<array> $objects
     *
     * @return BaseObject[]
     */
    public static function buildObjects($class, array $objects)
    {
        return array_map(function ($object) use ($class) {
            return self::convertToObject($class, $object);
        }, $objects);
    }
}
