<?php

/**
 * Database service for the Tags_Attributes table.
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
use GeebyDeeby\Db\Entity\TagsAttributeEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\TagsAttribute;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Tags_Attributes table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsAttributeService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param TagsAttribute      $tagsAttributeTable TagsAttribute table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected TagsAttribute $tagsAttributeTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return TagsAttributeEntityInterface
     */
    public function createEntity(): TagsAttributeEntityInterface
    {
        return $this->tagsAttributeTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?TagsAttributeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?TagsAttributeEntityInterface
    {
        return $this->tagsAttributeTable->getByPrimaryKey($id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param TagsAttributeEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(TagsAttributeEntityInterface $entity): ?string
    {
        $name = $entity->getAttributeName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of tag attributes.
     *
     * @return TagsAttributeEntityInterface[]
     */
    public function getList(): array
    {
        return iterator_to_array($this->tagsAttributeTable->getList());
    }
}
