<?php

/**
 * Database service for the Items_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAttribute;
use GeebyDeeby\Db\Entity\ItemsAttributesValue;
use GeebyDeeby\Db\Entity\ItemsAttributesValueEntityInterface;

/**
 * Database service for the Items_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAttributesValueService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsAttributesValueEntityInterface
     */
    public function createEntity(): ItemsAttributesValueEntityInterface
    {
        $entity = new ItemsAttributesValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of attributes for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getAttributesForItem(int $itemID): array
    {
        $dql = 'SELECT iav.value AS Items_Attribute_Value, ia.id AS Items_Attribute_ID, '
            . 'ia.attributeName AS Items_Attribute_Name, ia.rdfProperty AS Items_Attribute_RDF_Property, '
            . 'ia.allowHtml AS Allow_HTML, ia.displayPriority AS Display_Priority FROM '
            . ItemsAttributesValue::class . ' iav '
            . 'INNER JOIN ' . ItemsAttribute::class . ' ia ON iav.attribute=ia.id '
            . 'WHERE iav.item = :item ORDER BY ia.displayPriority, ia.attributeName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Delete existing attributes associated with the provided item.
     *
     * @param int|ItemEntityInterface $item Item entity or ID
     *
     * @return void
     */
    public function deleteByItem(int|ItemEntityInterface $item): void
    {
        $dql = 'DELETE FROM ' . ItemsAttributesValue::class . ' iav WHERE iav.item=:item';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $item instanceof ItemEntityInterface ? $item->getId() : $item);
        $query->execute();
    }
}
