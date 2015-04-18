<?php

namespace Domain;

interface IRepository {

    /**
     * Returns a row from the database by its table name and id
     *
     * @param $id string Identifier attribute name
     *
     * @return IEntity
     */
    public function retrieve($id);

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
}