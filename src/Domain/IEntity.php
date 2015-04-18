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
}