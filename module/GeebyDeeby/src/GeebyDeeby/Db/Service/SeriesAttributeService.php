<?php

/**
 * Database service for the Series_Attributes table.
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

use GeebyDeeby\Db\Entity\SeriesAttribute;
use GeebyDeeby\Db\Entity\SeriesAttributeEntityInterface;

/**
 * Database service for the Series_Attributes table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesAttributeService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesAttributeEntityInterface
     */
    public function createEntity(): SeriesAttributeEntityInterface
    {
        return new SeriesAttribute();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesAttributeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesAttributeEntityInterface
    {
        return $this->entityManager->find(SeriesAttribute::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param SeriesAttributeEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(SeriesAttributeEntityInterface $entity): ?string
    {
        $name = $entity->getAttributeName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of series attributes.
     *
     * @return SeriesAttributeEntityInterface[]
     */
    public function getList(): array
    {
        $dql = 'SELECT a FROM ' . SeriesAttribute::class . ' a ORDER BY a.attributeName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }
}
