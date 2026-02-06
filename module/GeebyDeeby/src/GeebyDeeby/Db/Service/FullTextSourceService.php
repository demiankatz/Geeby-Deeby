<?php

/**
 * Database service for the Full_Text_Sources table.
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
use GeebyDeeby\Db\Entity\FullTextSourceEntity;
use GeebyDeeby\Db\Entity\FullTextSourceEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\FullTextSource;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Full_Text_Sources table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FullTextSourceService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager       Entity manager
     * @param PersistenceManager $persistenceManager  Persistence manager
     * @param FullTextSource     $fullTextSourceTable FullTextSource table
     */
    public function __construct(
        protected EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected FullTextSource $fullTextSourceTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return FullTextSourceEntityInterface
     */
    public function createEntity(): FullTextSourceEntityInterface
    {
        return new FullTextSourceEntity();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?FullTextSourceEntityInterface
     */
    public function getByPrimaryKey(int $id): ?FullTextSourceEntityInterface
    {
        return $this->entityManager->find(FullTextSourceEntity::class, $id);
    }

    /**
     * Validate a populated entity -- return error message if problem found, null otherwise.
     *
     * @param FullTextSourceEntityInterface $entity Entity to validate
     *
     * @return ?string
     */
    public function getValidationError(FullTextSourceEntityInterface $entity): ?string
    {
        $name = $entity->getSourceName();
        return empty($name) ? 'Name cannot be blank.' : null;
    }

    /**
     * Get a list of sources.
     *
     * @param ?int $seriesID Series ID to limit (optional)
     *
     * @return FullTextSourceEntityInterface[]
     */
    public function getList(?int $seriesID = null): array
    {
        $dql = 'SELECT fts FROM ' . FullTextSourceEntity::class . ' fts';
        if ($seriesID) {
            throw new \Exception('TODO: implement series filtering');
        }
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
        //return iterator_to_array($this->fullTextSourceTable->getList($seriesID));
    }
}
