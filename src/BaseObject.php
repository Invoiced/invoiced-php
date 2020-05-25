<?php

namespace Invoiced;

use ArrayAccess;
use Exception;
use InvalidArgumentException;
use JsonSerializable;

class BaseObject implements ArrayAccess, JsonSerializable
{
    /**
     * @staticvar array properties that cannot be updated
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
     * @var array
     */
    protected $_values;

    /**
     * @var array
     */
    protected $_unsaved;

    /**
     * @param \Invoiced\Client $client API client instance
     * @param string           $id
     * @param array            $values
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
     * @return self
     */
    public function setEndpointBase($base)
    {
        $this->_endpointBase = $base;

        return $this;
    }

    // PHP magic methods

    public function __set($k, $v)
    {
        if ('' === $v) {
            throw new InvalidArgumentException(
                'You cannot set \''.$k.'\'to an empty string. '
                .'We interpret empty strings as NULL in requests. '
                .'You may set obj->'.$k.' = NULL to delete the property'
            );
        }

        if (!in_array($k, static::$permanentAttributes)) {
            $this->_values[$k] = $v;

            if (!in_array($k, $this->_unsaved)) {
                $this->_unsaved[] = $k;
            }
        }
    }

    public function __isset($k)
    {
        return isset($this->_values[$k]);
    }

    public function __unset($k)
    {
        unset($this->_values[$k]);
        if ($index = false !== array_search($k, $this->_unsaved)) {
            unset($this->_unsaved[$index]);
        }
    }

    public function &__get($k)
    {
        if (array_key_exists($k, $this->_values)) {
            return $this->_values[$k];
        } else {
            $class = get_class($this);

            throw new Exception("Undefined property of $class: $k");
        }
    }

    public function __toString()
    {
        $class = get_class($this);

        return $class.' JSON: '.$this->__toJSON();
    }

    // implements ArrayAccess

    public function offsetSet($k, $v)
    {
        $this->$k = $v;
    }

    public function offsetExists($k)
    {
        return array_key_exists($k, $this->_values);
    }

    public function offsetUnset($k)
    {
        unset($this->$k);
    }

    public function offsetGet($k)
    {
        return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
    }

    public function keys()
    {
        return array_keys($this->_values);
    }

    // implements JsonSerializable

    public function jsonSerialize()
    {
        return $this->__toArray(true);
    }

    public function __toJSON()
    {
        return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
    }

    public function __toArray()
    {
        return $this->_values;
    }

    // Object getters

    /**
     * Gets the client instance used by this object.
     *
     * @return \Invoiced\Client
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
     * @param string $id
     * @param array  $opts optional options to pass on
     *
     * @return \Invoiced\BaseObject
     */
    public function retrieve($id, array $opts = [])
    {
        if (!$id) {
            throw new InvalidArgumentException('Missing ID.');
        }

        $response = $this->_client->request('get', $this->getEndpoint()."/$id", $opts);

        return Util::convertToObject($this, $response['body']);
    }
}
