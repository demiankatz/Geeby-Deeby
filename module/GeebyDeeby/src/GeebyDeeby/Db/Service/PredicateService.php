<?php

/**
 * Database service for the Predicates table.
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

use GeebyDeeby\Db\Entity\PredicateEntityInterface;
use GeebyDeeby\Db\Table\Predicate;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param Predicate $predicateTable Predicate table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Predicate $predicateTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PredicateEntityInterface
     */
    public function createEntity(): PredicateEntityInterface
    {
        return $this->predicateTable->createRow();
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
        return $this->predicateTable->getByPrimaryKey($id);
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
        return iterator_to_array($this->predicateTable->getList());
    }
}
