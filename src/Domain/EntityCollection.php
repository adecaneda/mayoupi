<?php

namespace Domain;

class EntityCollection extends \ArrayObject {

    /**
     * Returns an array of entity attrs indexed by identifier.
     *
     * @param $withRelations bool
     * @return array
     */
    public function getAttrs($withRelations = false)
    {
        $items = [];

        /** @var $entity IEntity */
        foreach ($this as $entity) {
            $idField = $entity->getIdentifierField();

            if ($withRelations) {
                $items[$entity->get($idField)] = $entity->getWithRelations();

            } else {
                $items[$entity->get($idField)] = $entity->get();
            }
        }

        return $items;
    }
} 