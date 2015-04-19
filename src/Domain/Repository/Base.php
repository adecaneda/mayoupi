<?php

namespace Domain\Repository;

use Domain\EntityCollection;
use Domain\IEntity;
use Library\Registry;
use Library\Database;

class Base {

    static protected $instances = [];

    protected $field = '';
    protected $table = '';
    protected $entity = '';

    protected function __construct() { }

    /**
     * Returns an instance of the repository.
     *
     * @return Base
     */
    static public function get()
    {
        $namespace = get_called_class();
        if (!array_key_exists($namespace, self::$instances)) {
            self::$instances[$namespace] = new $namespace;
        }
        return self::$instances[$namespace];
    }

    /**
     * Returns a row from the database by its table name and id
     *
     * @param $id string Identifier attribute value
     * @param $field string Identifier attribute name (optional)
     *
     * @return IEntity
     */
    public function retrieve($id, $field = null)
    {
        $field = $field ?: $this->field;
        $class = "Domain\\Entity\\" . $this->entity;

        $attrs = $this->_retrieve($this->table, $field, $id);
        if (!$attrs) {
            return null;
        }

        return new $class($attrs);
    }

    /**
     * Retrieve all entities of a type.
     *
     * @return array EntityCollection::
     */
    public function retrieveAll()
    {
        $entities = new EntityCollection();

        foreach ($items = $this->_retrieveAll($this->table) as $item) {
            $class = "Domain\\Entity\\" . $this->entity;
            $entities[] = new $class($item);
        }
        return $entities;
    }

    /**
     * Persists an object (insert or update)
     *
     * @param $entity IEntity
     *
     * @return IEntity
     */
    public function persist($entity)
    {
        return $this->_persist($entity, $this->table, $this->field);
    }

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function remove($entity)
    {
        $this->_remove($entity, $this->table);
        $entity = null;
    }

    /**
     * Returns a row from the database by its table name and identifier. If there
     * are several rows with the same value only the first will be returned.
     *
     * @param $table string Table name
     * @param $id string Identifier attribute name
     * @param $value string Identifier attribute value
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function _retrieve($table, $id, $value)
    {
        /* @var $db Database */
        $db = Registry::getInstance()->db;

        $result = $db->query("SELECT * FROM `$table` WHERE `$id` = '$value'");
        if (false === $fields = mysql_fetch_assoc($result)) {
            $fields = array();
        }
        return $fields;
    }

    /**
     * Returns a row from the database by its table name and id
     *
     * @param $table string Table name
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function _retrieveAll($table)
    {
        /* @var $db Database */
        $db = Registry::getInstance()->db;

        $result = $db->query("SELECT * FROM `$table`");

        $items = [];
        while ($fields = mysql_fetch_assoc($result)) {
            $items[] = $fields;
        }
        return $items;
    }

    /**
     * Persists an object (insert or update)
     *
     * @param $entity IEntity
     * @param $table string Table name
     * @param $idAttr string Identifier attribute name
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function _persist($entity, $table, $idAttr)
    {
        /* @var $db Database */
        $db = Registry::getInstance()->db;

        $attrs = $entity->get();

        // special attribute 'password'
        if ($entity->hasAttr('password')) {
            $attrs['password'] = $entity->get('password');
        }

        // Update
        if (array_key_exists($idAttr, $attrs)) {
            $parts = array();
            foreach ($attrs as $key => $value) {
                $parts[] = "`$key` = '$value'";
            }
            $query = "UPDATE `$table` SET " . implode(',', $parts) . "  WHERE `$idAttr` = {$entity->get($idAttr)}";
            $db->query($query);

        // Insert
        } else {
            $keys = $values = array();
            foreach ($attrs as $key => $value) {
                $keys[] = "`$key`";
                $values[] = "'$value'";
            }
            $query = "INSERT INTO `$table` (" . implode(',', $keys) . ") VALUES (" . implode(',', $values). ")";
            if ($db->query($query)) {
                $attrs[$idAttr] = (string)mysql_insert_id();

                // Set basicly the id
                $entity->setAttrs($attrs);
            }
        }

        return $entity;
    }

    /**
     * @param $entity IEntity
     * @param $table string Table name
     *
     * @throws \Exception
     */
    protected function _remove($entity, $table)
    {
        $idAttr = $entity->getIdentifierField();
        if (!$entity->hasAttr($idAttr)) {
            throw new \Exception('Trying to remove an entity without id.');
        }

        /* @var $db Database */
        $db = Registry::getInstance()->db;

        $query = "DELETE FROM `$table` WHERE `$idAttr` = {$entity->get($idAttr)}";
        $db->query($query);
    }
} 