<?php

/**
 * Database service for the Publishers table.
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

use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Table\Publisher;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Publishers table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublisherService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Publisher $publisherTable Publisher table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Publisher $publisherTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PublisherEntityInterface
     */
    public function createEntity(): PublisherEntityInterface
    {
        return $this->publisherTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?PublisherEntityInterface
     */
    public function getByPrimaryKey(int $id): ?PublisherEntityInterface
    {
        return $this->publisherTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param PublisherEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(PublisherEntityInterface $entity): ?string
    {
        $name = $entity->getPublisherName();
        return empty($name) ? 'Publisher name cannot be blank.' : null;
    }

    /**
     * Get a list of publishers.
     *
     * @return PublisherEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->publisherTable->getList());
    }
}
