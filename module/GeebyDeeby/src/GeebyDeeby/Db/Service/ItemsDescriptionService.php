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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsDescriptionEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsDescriptions;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param EntityManager      $entityManager          Entity manager
     * @param PersistenceManager $persistenceManager     Persistence manager
     * @param ItemsDescriptions  $itemsDescriptionsTable ItemsDescriptions table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsDescriptions $itemsDescriptionsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsDescriptionEntityInterface
     */
    public function createEntity(): ItemsDescriptionEntityInterface
    {
        return $this->itemsDescriptionsTable->createRow();
    }

    /**
     * Get a list of descriptions for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getDescriptions(int $itemID): array
    {
        return iterator_to_array($this->itemsDescriptionsTable->getDescriptions($itemID));
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
        $where = [
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
            'Source' => $source instanceof ItemEntityInterface ? $source->getId() : $source,
        ];
        foreach ($this->itemsDescriptionsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
