<?php

namespace Library;

class View {

    /**
     * Data stored in the registry
     *
     * @var array $data
     * @access protected
     */
    protected $data;
    /**
     * @var View $instance
     * @access private static
     *
     * Speichert die einige Instanz dieser Klasse (Singleton)
     */
    private static $instance = null;

    /**
     * Base path for the templates
     * @var string
     */
    protected $basepath = null;

    /**
     * @var string Controller name
     */
    protected $controller = true;

    /**
     * @var string Action name
     */
    protected $action = true;

    /**
     * @return self
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
     */
    private function __construct() {}

    /**
     * Constructor disabled from outside
     */
    private function __clone() {}

    /**
     * Sets the base path for the templates.
     *
     * @param $path
     */
    public function setBasePath($path)
    {
        // Path for views
        $this->basepath = $path;
    }

    /**
     * Set data variables at once.
     *
     * @param $data
     */
    public function setData($data)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Adds a new value.
     *
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
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }

        return $this->data[$key];
    }

    /**
     * Checks if the given key already exists.
     *
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
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Returns the output for current data and the given template.
     *
     * @param $tpl
     * @return string
     *
     * @throws \Exception
     */
    public function output($tpl)
    {
        if (!$tpl) {
            throw new \Exception("A template is needed");
        }

        $path = $this->basepath . '\\' . $tpl;
        if (file_exists($path)) {
            ob_start();

            include($path);

            return ob_flush();

        } else {
            throw new \Exception("Template $path not found");
        }
    }
}