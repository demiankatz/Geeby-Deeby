<?php

/**
 * Database service for the Link_Types table.
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

use GeebyDeeby\Db\Entity\LinkTypeEntityInterface;
use GeebyDeeby\Db\Table\LinkType;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Link_Types table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class LinkTypeService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param LinkType $linkTypeTable LinkType table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected LinkType $linkTypeTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return LinkTypeEntityInterface
     */
    public function createEntity(): LinkTypeEntityInterface
    {
        return $this->linkTypeTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?LinkTypeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?LinkTypeEntityInterface
    {
        return $this->linkTypeTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param LinkTypeEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(LinkTypeEntityInterface $entity): ?string
    {
        $name = $entity->getLinkTypeName();
        return empty($name) ? 'Type cannot be blank.' : null;
    }

    /**
     * Get a list of link types.
     *
     * @return LinkTypeEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->linkTypeTable->getList());
    }
}
