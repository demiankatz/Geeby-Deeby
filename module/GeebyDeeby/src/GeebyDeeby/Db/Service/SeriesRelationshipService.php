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

use GeebyDeeby\Db\Entity\SeriesRelationship;
use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;

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
    use Feature\RelationshipOptionListTrait;

    /**
     * Create an empty entity.
     *
     * @return SeriesRelationshipEntityInterface
     */
    public function createEntity(): SeriesRelationshipEntityInterface
    {
        return new SeriesRelationship();
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
        return $this->entityManager->find(SeriesRelationship::class, $id);
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
        $dql = 'SELECT r FROM ' . SeriesRelationship::class . ' r ORDER BY r.relationshipName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }
}
