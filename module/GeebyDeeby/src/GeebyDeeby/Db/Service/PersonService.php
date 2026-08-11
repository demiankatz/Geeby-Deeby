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

use Doctrine\ORM\Tools\Pagination\Paginator as PaginationPaginator;
use GeebyDeeby\Db\DoctrinePaginatorAdapter;
use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionsCredit;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\Person;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use Laminas\Paginator\Paginator;

use function count;
use function intval;
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
     * Create an empty entity.
     *
     * @return PersonEntityInterface
     */
    public function createEntity(): PersonEntityInterface
    {
        $entity = new Person();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        return $this->entityManager->find(Person::class, $id);
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
        $dql = 'SELECT p FROM ' . Person::class . ' p'
            . ($biosOnly ? " WHERE p.biography != ''" : '')
            . ' ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
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
        $dql = 'SELECT p FROM ' . Person::class . ' p ORDER BY p.id DESC';
        $query = $this->entityManager->createQuery($dql);
        $query->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
        $doctrinePaginator = new PaginationPaginator($query);
        $doctrinePaginator->setUseOutputWalkers(false);
        $paginator = new \Laminas\Paginator\Paginator(
            new DoctrinePaginatorAdapter($doctrinePaginator)
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
        $dql = 'SELECT i.id AS Item_ID, '
            . 'p.id AS Person_ID, p.firstName AS First_Name, p.lastName AS Last_Name, p.extraDetails AS Extra_Details '
            . 'FROM ' . Person::class . ' p '
            . 'INNER JOIN ' . EditionsCredit::class . ' ec ON ec.person=p.id '
            . 'INNER JOIN ' . Edition::class . ' e ON ec.edition=e.id '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'WHERE i.id IN (:items) '
            . 'ORDER BY p.lastName, p.firstName, p.extraDetails';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('items', $itemIds);
        return $query->getResult();
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
        $parts = preg_split("/[\s,]+/", $query);
        $first = $parts[0];
        $params = ['first' => $first . '%'];
        $c = count($parts);
        $last = ($c > 1) ? $parts[$c - 1] : null;
        $dql = 'SELECT p FROM ' . Person::class . ' p WHERE (p.firstName LIKE :first OR p.lastName LIKE :first';
        if (intval($first) > 0) {
            $dql .= ' OR p.id=:first';
        }
        $dql .= ')';
        if ($last) {
            $dql .= ' AND (p.firstName LIKE :last OR p.lastName LIKE :last)';
            $params['last'] = $last;
        }
        $dql .= ' ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        if ($limit) {
            $query->setMaxResults($limit);
        }
        return $query->getResult();
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
        $where = array_map(
            fn ($i) => "(p.firstName LIKE ?$i OR p.lastName LIKE ?$i OR p.extraDetails LIKE ?$i)",
            array_keys($tokens)
        );
        $dql = 'SELECT p FROM ' . Person::class . ' p WHERE '
            . implode(' AND ', $where) . ' ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(array_map(fn ($token) => "%$token%", $tokens));
        return $query->getResult();
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
        $dql = 'SELECT p FROM ' . Person::class . ' p WHERE p.lastName=:last';
        $params = compact('last');
        if (!empty($first)) {
            $dql .= ' AND p.firstName=:first';
            $params['first'] = $first;
        }
        if (!empty($extra)) {
            $dql .= ' AND p.extraDetails=:extra';
            $params['extra'] = $extra;
        }
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $result = $query->getResult();
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
        $params = ['last' => $last . '%'];
        $dql = 'SELECT p FROM ' . Person::class . ' p WHERE p.lastName LIKE :last';
        if (strlen($first) > 0) {
            $params['initial'] = substr($first, 0, 1) . '%';
            $dql .= ' AND p.firstName LIKE :initial';
        }
        $dql .= ' ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $result = $query->getResult();
        if (count($result) === 0 && $allowFuzzier) {
            $fuzzierDql = 'SELECT p FROM ' . Person::class . ' p WHERE p.lastName LIKE :chunk'
                . ' ORDER BY p.lastName, p.firstName, p.extraDetails, p.id';
            $fuzzierQuery = $this->entityManager->createQuery($fuzzierDql);
            $fuzzierQuery->setParameter('chunk', substr($last, 0, strlen($last) - 1) . '%');
            return $fuzzierQuery->getResult();
        }
        return $result;
    }
}
