<?php

/**
 * Database service for the Series table.
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

use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Table\Series;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Paginator\Paginator;

/**
 * Database service for the Series table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Series $seriesTable Series table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Series $seriesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesEntityInterface
     */
    public function createEntity(): SeriesEntityInterface
    {
        return $this->seriesTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesEntityInterface
    {
        return $this->seriesTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param SeriesEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(SeriesEntityInterface $entity): ?string
    {
        $name = $entity->getSeriesName();
        return empty($name) ? 'Series name cannot be blank.' : null;
    }

    /**
     * Get a list of countries.
     *
     * @return SeriesEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->seriesTable->getList());
    }

    /**
     * Get a list of series in the specified language.
     *
     * @param int $langID Language ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForLanguage(int $langID): array
    {
        return iterator_to_array($this->seriesTable->getSeriesForLanguage($langID));
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return array
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        return iterator_to_array($this->seriesTable->getSuggestions($query, $limit));
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
        return iterator_to_array($this->seriesTable->keywordSearch($tokens));
    }

    /**
     * Get a paginator populated with new series.
     *
     * @param int $page     Result page to load
     * @param int $pageSize Result count per page
     *
     * @return Paginator
     */
    public function getNewSeriesPaginator(int $page = 1, int $pageSize = 50): Paginator
    {
        $adapter = $this->seriesTable->getAdapter();
        $query = new \Laminas\Db\Sql\Select($this->seriesTable->getTable());
        $query->order('Series_ID DESC');
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

    /**
     * Get a list of series for the specified item.
     *
     * @param int  $itemID                Item ID
     * @param bool $includePosition       Should we include position information?
     * @param bool $includeParentPosition Should we include information about parent edition(s)?
     *
     * @return array
     */
    public function getSeriesForItem(
        int $itemID,
        bool $includePosition = true,
        bool $includeParentPosition = false
    ): array {
        return iterator_to_array(
            $this->seriesTable->getSeriesForItem($itemID, $includePosition, $includeParentPosition)
        );
    }
}
