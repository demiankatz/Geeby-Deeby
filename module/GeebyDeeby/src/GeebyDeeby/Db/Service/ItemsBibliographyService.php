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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsBibliographyEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsBibliography;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param PersistenceManager $persistenceManager     Persistence manager
     * @param ItemsBibliography  $itemsBibliographyTable ItemsBibliography table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsBibliography $itemsBibliographyTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsBibliographyEntityInterface
     */
    public function createEntity(): ItemsBibliographyEntityInterface
    {
        return $this->itemsBibliographyTable->createRow();
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
        return iterator_to_array($this->itemsBibliographyTable->getItemsDescribingItem($itemID));
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
        return iterator_to_array($this->itemsBibliographyTable->getItemsDescribedByItem($itemID));
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
        $where = [
            'Bib_Item_ID' => $bib instanceof ItemEntityInterface ? $bib->getId() : $bib,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        foreach ($this->itemsBibliographyTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
