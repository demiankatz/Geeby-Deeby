<?php

/**
 * Database service for the Items table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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
use GeebyDeeby\Db\Table\Item;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Paginator\Paginator;

/**
 * Database service for the Items table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Item $itemTable Item table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Item $itemTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return ItemEntityInterface
     */
    public function createEntity(): ItemEntityInterface
    {
        return $this->itemTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?ItemEntityInterface
     */
    public function getByPrimaryKey(int $id): ?ItemEntityInterface
    {
        return $this->itemTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param ItemEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(ItemEntityInterface $entity): ?string
    {
        $name = $entity->getItemName();
        return empty($name) ? 'Item name cannot be blank.' : null;
    }

    /**
     * Get a list of items.
     *
     * @return ItemEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->itemTable->getList());
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return mixed
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        return iterator_to_array($this->itemTable->getSuggestions($query, $limit));
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return array
     */
    public function keywordSearch(array $tokens): array
    {
        return iterator_to_array($this->itemTable->keywordSearch($tokens));
    }

    /**
     * Get a list of items for the specified series.
     *
     * @param int  $seriesID        Series ID
     * @param bool $topOnly         Retrieve only top-level items?
     * @param bool $groupByMaterial Should we group results by material type?
     *
     * @return mixed
     */
    public function getItemsForSeries(
        int $seriesID,
        bool $topOnly = true,
        bool $groupByMaterial = true
    ): array {
        return iterator_to_array($this->itemTable->getItemsForSeries($seriesID, $topOnly, $groupByMaterial));
    }

    /**
     * Get a list of children for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemChildren(int $itemID): array
    {
        return iterator_to_array($this->itemTable->getItemChildren($itemID));
    }

    /**
     * Get a list of parents for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemParents(int $itemID): array
    {
        return iterator_to_array($this->itemTable->getItemParents($itemID));
    }

    /**
     * Get a list of items for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getItemsForEdition(int $editionID): array
    {
        return iterator_to_array($this->itemTable->getItemsForEdition($editionID));
    }

    /**
     * Get items with online full text associated with a person
     *
     * @param int $personId Person_ID to filter by
     *
     * @return array
     */
    public function getItemsWithFullTextByPerson(int $personId): array
    {
        return iterator_to_array($this->itemTable->getItemsWithFullTextByPerson($personId));
    }

    /**
     * Get a paginator populated with new items.
     *
     * @param int $page     Result page to load
     * @param int $pageSize Result count per page
     *
     * @return Paginator
     */
    public function getNewItemsPaginator(int $page = 1, int $pageSize = 50): Paginator
    {
        $adapter = $this->itemTable->getAdapter();
        $query = new \Laminas\Db\Sql\Select($this->itemTable->getTable());
        $query->order('Item_ID DESC');
        $paginator = new \Laminas\Paginator\Paginator(
            new \Laminas\Paginator\Adapter\DbSelect(
                $query,
                $adapter
            )
        );
        $paginator->setItemCountPerPage($pageSize);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }
}
