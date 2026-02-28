<?php

/**
 * Database service for the Series_Relationships table.
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
use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesRelationship;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Relationships table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesRelationshipService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager           Entity manager
     * @param PersistenceManager $persistenceManager      Persistence manager
     * @param SeriesRelationship $seriesRelationshipTable SeriesRelationship table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesRelationship $seriesRelationshipTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesRelationshipEntityInterface
     */
    public function createEntity(): SeriesRelationshipEntityInterface
    {
        return $this->seriesRelationshipTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesRelationshipEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesRelationshipEntityInterface
    {
        return $this->seriesRelationshipTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param SeriesRelationshipEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(SeriesRelationshipEntityInterface $entity): ?string
    {
        $name = $entity->getRelationshipName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of series relationships.
     *
     * @return SeriesRelationshipEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->seriesRelationshipTable->getList());
    }

    /**
     * Get a list of relationships, formatted to populate a select control.
     *
     * @param bool $includePredicate Should we return labels only (false) or an
     * array with label and predicate (true)?
     *
     * @return array
     */
    public function getOptionList(bool $includePredicate = false): array
    {
        return $this->seriesRelationshipTable->getOptionList($includePredicate);
    }
}
