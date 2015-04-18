<?php

namespace Library;

/**
 * Class Database
 *
 * Simple database access
 */
class Database {

    /**
     * @var int Database connection id
     */
    protected $id;

    /**
     * Creates a connection to the database using the given configuration
     *
     * @param $conf array Must contain these keys: 'host', 'user', 'pass', 'name'
     */
    public function __construct($conf)
    {
        if (!$this->id = mysql_connect($conf['host'], $conf['user'], $conf['pass'])) {
            echo mysql_error($this->id);
        }
        // Encoding stuff
        @mysql_query("SET character_set_results=utf8", $this->id);
        @mb_language('uni');
        @mb_internal_encoding('UTF-8');
        if (!$this->_selectDb = @mysql_select_db($conf['name'], $this->id)) {
            echo mysql_error($this->id);
        }
        // More encoding stuff
        @mysql_query("set names 'utf8'", $this->id);
    }
    /**
     * Executes the given query
     *
     * @param string $query
     * @return resource
     */
    public function query($query)
    {
        if (false === $result = mysql_query($query, $this->id)) {
            echo mysql_error($this->id);
            return null;
        }
        return $result;
    }
};