<?php

/**
 * Database service for the People table.
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

use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Table\Person;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Paginator\Paginator;

/**
 * Database service for the People table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersonService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Person $personTable Person table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Person $personTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PersonEntityInterface
     */
    public function createEntity(): PersonEntityInterface
    {
        return $this->personTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?PersonEntityInterface
     */
    public function getByPrimaryKey(int $id): ?PersonEntityInterface
    {
        return $this->personTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param PersonEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(PersonEntityInterface $entity): ?string
    {
        $name = $entity->getLastName();
        return empty($name) ? 'Last name cannot be blank.' : null;
    }

    /**
     * Get a list of people.
     *
     * @param bool $biosOnly Should we filter to only people with biographies?
     *
     * @return PersonEntityInterface[]
     */
    public function getList(bool $biosOnly = false): array
    {
        return iterator_to_array($this->personTable->getList($biosOnly));
    }

    /**
     * Get a paginator populated with new people.
     *
     * @param int $page     Result page to load
     * @param int $pageSize Result count per page
     *
     * @return Paginator
     */
    public function getNewPeoplePaginator(int $page = 1, int $pageSize = 50): Paginator
    {
        $adapter = $this->personTable->getAdapter();
        $query = new \Laminas\Db\Sql\Select($this->personTable->getTable());
        $query->order('Person_ID DESC');
        $paginator = new Paginator(
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
     * Get people for item IDs.
     *
     * @param array $itemIds Item IDs to match.
     *
     * @return PersonEntityInterface[]
     */
    public function getListForItemIds(array $itemIds): array
    {
        return iterator_to_array($this->personTable->getListForItemIds($itemIds));
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return PersonEntityInterface[]
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        return iterator_to_array($this->personTable->getSuggestions($query, $limit));
    }

    /**
     * Perform a keyword search.
     *
     * @param array $tokens Keywords.
     *
     * @return PersonEntityInterface[]
     */
    public function keywordSearch(array $tokens): array
    {
        return iterator_to_array($this->personTable->keywordSearch($tokens));
    }
}
