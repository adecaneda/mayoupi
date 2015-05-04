<?php

namespace Domain\Entity;

use Domain\IEntity;

class Base implements IEntity {

    /**
     * @var array Stores all object attributes as a pair key-value
     */
    protected $attrs;

    /**
     * Creates an instance of the entity from its values
     *
     * @param $attrs array Array with entity attributes
     */
    public function __construct ($attrs)
    {
        $this->attrs = $attrs;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->attrs[$this->getIdentifierField()];
    }

    /**
     * The default field is 'id'.
     *
     * @return string
     */
    public function getIdentifierField()
    {
        return 'id';
    }

    /**
     * Set several attributes at once. A try to modify the entity identifier
     * will throw an exception.
     *
     * @param $attrs
     *
     * @throws \Exception
     */
    public function setAttrs($attrs)
    {
        $idField = $this->getIdentifierField();
        if ($this->hasAttr($idField)) {
            $oldId = $this->get($idField);
            // throw exception on different ids
            if (array_key_exists($idField, $attrs) && $attrs[$idField] !== $oldId) {
                throw new \Exception('Trying to modify an existing entity identifier');
            }
        }

        $this->attrs = array_merge($this->attrs, $attrs);
    }

    /**
     * Returns the attributes of the entity and its related sub-entities.
     *
     * @return array
     */
    public function getWithRelations()
    {
        return $this->get();
    }

    /**
     * Gets the value of an (existing) attribute
     *
     * @param $attr
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($attr = null)
    {
        // return all fields is no attr is specified
        if ($attr === null) {
            return $this->attrs;

        // throw exception if the attr doesn't exist
        } else if (!$this->hasAttr($attr)) {
            throw new \Exception("Unknown attribute '$attr' in class '" . get_called_class() . "'");
        }

        return $this->attrs[$attr];
    }

    /**
     * Sets the value of an (existing) attribute
     *
     * @param $attr
     * @param $value
     *
     * @return mixed
     * @throws \Exception
     */
    protected function set($attr, $value)
    {
        if (!array_key_exists($attr, $this->attrs)) {
            throw new \Exception("Unknown attribute '$attr' in class '" . get_called_class() . "'");
        }
        $this->attrs[$attr] = $value;
    }

    /**
     * Returns true if the attribute $attr exists in the entity. Otherwise false.
     *
     * @param $attr
     * @return bool
     */
    public function hasAttr($attr)
    {
        return array_key_exists($attr, $this->attrs);
    }
}