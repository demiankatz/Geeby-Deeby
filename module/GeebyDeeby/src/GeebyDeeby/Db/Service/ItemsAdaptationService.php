<?php

/**
 * Database service for the Items_Adaptations table.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAdaptation;
use GeebyDeeby\Db\Entity\ItemsAdaptationEntityInterface;
use GeebyDeeby\Db\Entity\MaterialType;

/**
 * Database service for the Items_Adaptations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAdaptationService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsAdaptationEntityInterface
     */
    public function createEntity(): ItemsAdaptationEntityInterface
    {
        $entity = new ItemsAdaptation();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of items adapted from the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAdaptedFrom(int $itemID): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, '
            . 'm.id AS Material_Type_ID, m.singularName AS Material_Type_Name, '
            . 'm.pluralName AS Material_Type_Plural_Name '
            . 'FROM ' . ItemsAdaptation::class . ' ia '
            . 'INNER JOIN ' . Item::class . ' i ON ia.adaptedItem = i.id '
            . 'INNER JOIN ' . MaterialType::class . ' m ON i.materialType = m.id '
            . 'WHERE ia.sourceItem = :item ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a list of items adapted into the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAdaptedInto(int $itemID): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, '
            . 'm.id AS Material_Type_ID, m.singularName AS Material_Type_Name, '
            . 'm.pluralName AS Material_Type_Plural_Name '
            . 'FROM ' . ItemsAdaptation::class . ' ia '
            . 'INNER JOIN ' . Item::class . ' i ON ia.sourceItem = i.id '
            . 'INNER JOIN ' . MaterialType::class . ' m ON i.materialType = m.id '
            . 'WHERE ia.adaptedItem = :item ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a row matching the provided source item/adapted item pair.
     *
     * @param int|ItemEntityInterface $source  Source item ID or entity
     * @param int|ItemEntityInterface $adapted Adapted item ID or entity
     *
     * @return ?ItemsAdaptationEntityInterface
     */
    public function getBySourceItemAndAdaptedItem(
        int|ItemEntityInterface $source,
        int|ItemEntityInterface $adapted
    ): ?ItemsAdaptationEntityInterface {
        $dql = 'SELECT ia FROM ' . ItemsAdaptation::class
            . ' ia WHERE ia.sourceItem=:source AND ia.adaptedItem=:adapted';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source instanceof ItemEntityInterface ? $source->getId() : $source);
        $query->setParameter('adapted', $adapted instanceof ItemEntityInterface ? $adapted->getId() : $adapted);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
