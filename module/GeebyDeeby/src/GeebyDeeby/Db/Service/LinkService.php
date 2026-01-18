<?php

/**
 * Database service for the Links table.
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

use DateTime;
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Table\Link;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Links table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class LinkService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Link $linkTable Link table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Link $linkTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return LinkEntityInterface
     */
    public function createEntity(): LinkEntityInterface
    {
        return $this->linkTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?LinkEntityInterface
     */
    public function getByPrimaryKey(int $id): ?LinkEntityInterface
    {
        return $this->linkTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param LinkEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(LinkEntityInterface $entity): ?string
    {
        if (!$entity->getLinkName()) {
            return 'Name cannot be blank.';
        }
        if (!$entity->getUrl()) {
            return 'URL cannot be blank.';
        }
        $checked = $entity->getDateChecked();
        return $checked instanceof DateTime ? null : 'Date must match YYYY-MM-DD format.';
    }

    /**
     * Get a list of links.
     *
     * @return LinkEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->linkTable->getList());
    }

    /**
     * Get a list of links grouped by type.
     *
     * @param ?string $typeFilter A filter for matching certain types (null for all)
     *
     * @return array
     */
    public function getListByType(?string $typeFilter = null): array
    {
        return iterator_to_array($this->linkTable->getListByType($typeFilter));
    }
}
