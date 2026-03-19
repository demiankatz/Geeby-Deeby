<?php

/**
 * Database service for the Items_Descriptions table.
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
use GeebyDeeby\Db\Entity\ItemsDescription;
use GeebyDeeby\Db\Entity\ItemsDescriptionEntityInterface;

/**
 * Database service for the Items_Descriptions table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsDescriptionService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsDescriptionEntityInterface
     */
    public function createEntity(): ItemsDescriptionEntityInterface
    {
        $entity = new ItemsDescription();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of descriptions for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return ItemsDescriptionEntityInterface[]
     */
    public function getDescriptions(int $itemID): array
    {
        $dql = 'SELECT d FROM ' . ItemsDescription::class . ' d WHERE d.item=:item ORDER BY d.source';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a row matching the provided item and source.
     *
     * @param int|ItemEntityInterface $item   Item ID or entity
     * @param string                  $source Source
     *
     * @return ?ItemsDescriptionEntityInterface
     */
    public function getByItemAndSource(
        int|ItemEntityInterface $item,
        string $source
    ): ?ItemsDescriptionEntityInterface {
        $dql = 'SELECT d FROM ' . ItemsDescription::class . ' d WHERE d.item=:item AND d.source=:source';
        $itemId = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['item' => $itemId, 'source' => $source]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
