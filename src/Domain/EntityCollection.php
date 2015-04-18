<?php

namespace Domain;

class EntityCollection extends \ArrayObject {

    /**
     * Returns an array of entity attrs indexed by identifier.
     *
     * @return array
     */
    public function getAttrs()
    {
        $items = [];

        /** @var $entity IEntity */
        foreach ($this as $entity) {
            $idField = $entity->getIdentifierField();
            $items[$entity->get($idField)] = $entity->get();
        }

        return $items;
    }
} 