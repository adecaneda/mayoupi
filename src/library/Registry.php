<?php

namespace Library;

/**
 * Class Registry
 *
 * Implements a very simple registry
 */
class Registry
{
    /**
     * Data stored in the registry
     *
     * @var array $data
     * @access protected
     */
    protected $data;

    /**
     * Stores the one instance of the class (Singleton pattern)
     *
     * @var Registry $_instance
     * @access private static
     */
    private static $instance = null;

    /**
     * Getter method.
     *
     * @access public static
     * @return Registry
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor disabled from outside
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Clone disabled from outside
     *
     * @access private
     */
    private function __clone() {}

    /**
     * Adds a new value.
     *
     * @access public
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Returns a value by its key. Null if key does not exist.
     *
     * @access public
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * Checks if the given key already exists.
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Removes a value by its key, if it exists.
     *
     * @access public
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }
}