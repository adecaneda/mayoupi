<?php
/**
 * Created by PhpStorm.
 * User: Ale
 * Date: 18.04.15
 * Time: 14:56
 */

namespace Domain;

interface IEntity {

    /**
     * Construction must be through an array of key => value.
     *
     * @param $attrs
     */
    public function __construct ($attrs);

    /**
     * Gets the value of an (existing) attribute
     *
     * @param $attr
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($attr = null);

    /**
     * Returns the name of the field that acts a domain identifier.
     *
     * @return string
     */
    public function getIdentifierField();

    /**
     * Returns the attributes of the entity and its related sub-entities.
     *
     * @return array
     */
    public function getWithRelations();

    /**
     * Set several attributes at once. A try to modify the entity identifier
     * will throw an exception.
     *
     * @param $attrs
     *
     * @throws \Exception
     */
    public function setAttrs($attrs);

    /**
     * Returns true if the entity has the attribute $attr.
     *
     * @param $attr string
     * @return mixed
     */
    public function hasAttr($attr);
}