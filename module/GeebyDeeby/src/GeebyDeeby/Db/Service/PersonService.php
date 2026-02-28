<?php

/**
 * Database service for the People table.
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
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\Person;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\Paginator\Paginator;

use function count;
use function strlen;

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
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param Person             $personTable        Person table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Person $personTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
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

    /**
     * Get an exact match for the provided name parts (null if not found).
     *
     * @param string $first First name to search
     * @param string $last  Last name to search
     * @param string $extra Extra details
     *
     * @return ?PersonEntityInterface
     */
    public function getExactMatch(string $first, string $last, string $extra): ?PersonEntityInterface
    {
        $query = [
            'Last_Name' => $last,
        ];
        if (!empty($first)) {
            $query['First_Name'] = $first;
        }
        if (!empty($extra)) {
            $query['Extra_Details'] = $extra;
        }
        $result = $this->personTable->select($query);
        if (count($result) >= 1) {
            foreach ($result as $current) {
                if (empty($first) && strlen($current->getFirstName())) {
                    continue;
                }
                if ($current->getExtraDetails() == $extra) {
                    return $current;
                }
            }
        }
        return null;
    }

    /**
     * Find people with similar names to the provided values.
     *
     * @param string $first        First name to search
     * @param string $last         Last name to search
     * @param bool   $allowFuzzier If no first+last match is found, should we fall back to last-only matches?
     *
     * @return PersonEntityInterface[]
     */
    public function getFuzzyMatches(string $first, string $last, bool $allowFuzzier = true): array
    {
        $callback = function ($select) use ($first, $last): void {
            if (strlen($first) > 0) {
                $initial = substr($first, 0, 1);
                $select->where->like('First_Name', $initial . '%');
            }
            $select->where->like('Last_Name', $last);
            $select->order(['Last_Name', 'First_Name']);
        };
        $result = $this->personTable->select($callback);
        if (count($result) === 0 && $allowFuzzier) {
            $fuzzierCallback = function ($select) use ($last): void {
                $chunk = substr($last, 0, strlen($last) - 1);
                $select->where->like('Last_Name', $chunk . '%');
                $select->order(['Last_Name', 'First_Name']);
            };
            $result = $this->personTable->select($fuzzierCallback);
        }
        return iterator_to_array($result);
    }
}
