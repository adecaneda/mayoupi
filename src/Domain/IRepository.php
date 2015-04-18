<?php

namespace Domain;

interface IRepository {

    /**
     * Returns a row from the database by its table name and id
     *
     * @param $id string Identifier attribute value
     * @param $field string Identifier attribute name (optional)
     *
     * @return IEntity
     */
    public function retrieve($id, $field = null);

    /**
     * Retrieve all entities of a type.
     *
     * @return ICollection
     */
    public function retrieveAll();

    /**
     * Persists an object (insert or update)
     *
     * @param $entity Entity\Base
     *
     * @return IEntity
     */
    public function persist($entity);

    /**
     * @param $entity
     *
     * @return mixed
     */
    public function remove($entity);
}