<?php

namespace Domain;

class EntityCollection extends \ArrayObject {

    /**
     * Returns an array of entity attrs indexed by identifier.
     *
     * @param $withRelations bool
     * @return array
     */
    public function getAttrs($withRelations = false, $indexed = true)
    {
        $items = [];

        /** @var $entity IEntity */
        foreach ($this as $entity) {
            if ($withRelations) {
                $item = $entity->getWithRelations();
            } else {
                $item = $entity->get();
            }

            if ($indexed) {
                $idField = $entity->getIdentifierField();
                $items[$entity->get($idField)] = $item;
            } else {
                $items[] = $item;
            }
        }

        return $items;
    }
} 