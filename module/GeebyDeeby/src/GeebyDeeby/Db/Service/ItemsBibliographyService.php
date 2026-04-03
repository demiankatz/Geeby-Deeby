<?php

/**
 * Database service for the Items_Bibliography table.
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
use GeebyDeeby\Db\Entity\ItemsBibliography;
use GeebyDeeby\Db\Entity\ItemsBibliographyEntityInterface;
use GeebyDeeby\Db\Entity\MaterialType;

/**
 * Database service for the Items_Bibliography table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsBibliographyService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsBibliographyEntityInterface
     */
    public function createEntity(): ItemsBibliographyEntityInterface
    {
        $entity = new ItemsBibliography();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of items describing the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemsDescribingItem(int $itemID): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, mt.id AS Material_Type_ID, '
            . 'mt.singularName AS Material_Type_Name, mt.pluralName AS Material_Type_Plural_Name '
            . 'FROM ' . ItemsBibliography::class . ' ib '
            . 'INNER JOIN ' . Item::class . ' i ON ib.bibliographyItem=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' mt ON i.materialType=mt.id '
            . 'WHERE ib.item = :item ORDER BY mt.singularName, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a list of items described by the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemsDescribedByItem(int $itemID): array
    {
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name '
            . 'FROM ' . ItemsBibliography::class . ' ib '
            . 'INNER JOIN ' . Item::class . ' i ON ib.item=i.id '
            . 'WHERE ib.bibliographyItem = :item ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve the entity for a particular bibliography entry.
     *
     * @param int|ItemEntityInterface $bib  Bibliography Item ID or entity
     * @param int|ItemEntityInterface $item Subject Item ID or entity
     *
     * @return ?ItemsBibliographyEntityInterface
     */
    public function getByBibliographyItemAndItem(
        int|ItemEntityInterface $bib,
        int|ItemEntityInterface $item
    ): ?ItemsBibliographyEntityInterface {
        $params = [
            'bib' => $bib instanceof ItemEntityInterface ? $bib->getId() : $bib,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT ib FROM ' . ItemsBibliography::class
            . ' ib WHERE ib.item = :item AND ib.bibliographyItem = :bib';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
