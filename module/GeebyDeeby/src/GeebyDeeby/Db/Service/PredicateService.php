<?php

/**
 * Database service for the Predicates table.
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

use GeebyDeeby\Db\Entity\Predicate;
use GeebyDeeby\Db\Entity\PredicateEntityInterface;

/**
 * Database service for the Predicates table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PredicateService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return PredicateEntityInterface
     */
    public function createEntity(): PredicateEntityInterface
    {
        return new Predicate();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?PredicateEntityInterface
     */
    public function getByPrimaryKey(int $id): ?PredicateEntityInterface
    {
        return $this->entityManager->find(Predicate::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param PredicateEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(PredicateEntityInterface $entity): ?string
    {
        if (empty($entity->getPredicate())) {
            return 'Predicate cannot be blank.';
        }
        $abbrev = $entity->getAbbreviation();
        return empty($abbrev) ? 'Predicate abbreviation cannot be blank.' : null;
    }

    /**
     * Get a list of file types.
     *
     * @return PredicateEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT p FROM ' . Predicate::class . ' p ORDER BY p.abbreviation';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return PredicateEntityInterface[]
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        $dql = 'SELECT p FROM ' . Predicate::class . ' p WHERE p.abbreviation LIKE :query'
            . ' ORDER BY p.abbreviation';
        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', $query . '%');
        if ($limit) {
            $queryObj->setMaxResults($limit);
        }
        return $queryObj->getResult();
    }
}
