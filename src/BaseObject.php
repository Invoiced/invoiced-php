<?php

namespace Invoiced;

use ArrayAccess;
use Exception;
use InvalidArgumentException;
use JsonSerializable;

/**
 * @property string|int $id
 * @property string     $object
 *
 * @implements ArrayAccess<string,mixed>
 */
class BaseObject implements ArrayAccess, JsonSerializable
{
    /**
     * @var array<string> properties that cannot be updated
     */
    public static $permanentAttributes = ['id'];

    /**
     * @var Client
     */
    protected $_client;

    /**
     * @var string
     */
    protected $_endpoint = '';

    /**
     * @var string
     */
    protected $_endpointBase;

    /**
     * @var array<mixed>
     */
    protected $_values;

    /**
     * @var array<mixed>
     */
    protected $_unsaved;

    /**
     * @param \Invoiced\Client $client API client instance
     * @param string|int       $id
     * @param array<mixed>     $values
     */
    public function __construct(Client $client, $id = null, array $values = [])
    {
        $this->_client = $client;
        $this->_values = [];

        if (null !== $id) {
            $this->_endpoint .= '/'.$id;
            $this->_values = array_replace($values, ['id' => $id]);
            $this->_unsaved = [];
        }
    }

    /**
     * Sets the endpoint base for this object.
     *
     * @param string $base
     *
     * @return $this
     */
    public function setEndpointBase($base)
    {
        $this->_endpointBase = $base;

        return $this;
    }

    // PHP magic methods

    /**
     * @param string $k
     * @param mixed  $v
     *
     * @return void
     */
    public function __set($k, $v)
    {
        if ('' === $v) {
            throw new InvalidArgumentException('You cannot set \''.$k.'\'to an empty string. '.'We interpret empty strings as NULL in requests. '.'You may set obj->'.$k.' = NULL to delete the property');
        }

        if (!in_array($k, static::$permanentAttributes)) {
            $this->_values[$k] = $v;

            if (!in_array($k, $this->_unsaved)) {
                $this->_unsaved[] = $k;
            }
        }
    }

    /**
     * @param string $k
     *
     * @return bool
     */
    public function __isset($k)
    {
        return isset($this->_values[$k]);
    }

    /**
     * @param string $k
     *
     * @return void
     */
    public function __unset($k)
    {
        unset($this->_values[$k]);
        if ($index = false !== array_search($k, $this->_unsaved)) {
            unset($this->_unsaved[$index]);
        }
    }

    /**
     * @param string $k
     *
     * @return mixed
     */
    public function &__get($k)
    {
        if (array_key_exists($k, $this->_values)) {
            return $this->_values[$k];
        } else {
            $class = get_class($this);

            throw new Exception("Undefined property of $class: $k");
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $class = get_class($this);

        return $class.' JSON: '.$this->__toJSON();
    }

    // implements ArrayAccess

    /**
     * @param string $k
     * @param mixed  $v
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($k, $v)
    {
        $this->$k = $v;
    }

    /**
     * @param string $k
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($k)
    {
        return array_key_exists($k, $this->_values);
    }

    /**
     * @param string $k
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($k)
    {
        unset($this->$k);
    }

    /**
     * @param string $k
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($k)
    {
        return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
    }

    /**
     * @return array<string>
     */
    public function keys()
    {
        return array_keys($this->_values);
    }

    // implements JsonSerializable

    /**
     * @return array<mixed>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * @return string|false
     */
    public function __toJSON()
    {
        return json_encode($this->__toArray(), JSON_PRETTY_PRINT);
    }

    /**
     * @return array<mixed>
     */
    public function __toArray()
    {
        return $this->_values;
    }

    // Object getters

    /**
     * Gets the client instance used by this object.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Gets the endpoint for this object.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->_endpointBase.$this->_endpoint;
    }

    /**
     * Gets the endpoint base for this object. Defaults to blank.
     *
     * @return string
     */
    public function getEndpointBase()
    {
        return $this->_endpointBase;
    }

    /**
     * Retrieves an instance of this object given an ID.
     *
     * @param string|int   $id
     * @param array<mixed> $opts optional options to pass on
     *
     * @return static
     */
    public function retrieve($id, array $opts = [])
    {
        if (!$id) {
            throw new InvalidArgumentException('Missing ID.');
        }

        $response = $this->_client->request('get', $this->getEndpoint()."/$id", $opts);

        return Util::convertToObject($this, $response['body']); /* @phpstan-ignore-line */
    }
}
