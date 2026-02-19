<?php

/**
 * Database service for the Publishers table.
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
use GeebyDeeby\Db\Entity\Publisher;
use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
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
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     */
    #[Autowire()]
    public function __construct(
        protected EntityManager $entityManager,
        PersistenceManager $persistenceManager,
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return PublisherEntityInterface
     */
    public function createEntity(): PublisherEntityInterface
    {
        return new Publisher();
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
        return $this->entityManager->find(Publisher::class, $id);
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
        $dql = 'SELECT p FROM ' . Publisher::class . ' p ORDER BY p.publisherName';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get autocomplete suggestions.
     *
     * @param string $query The user query.
     * @param ?int   $limit Limit on returned rows (null for no limit).
     *
     * @return array
     */
    public function getSuggestions(string $query, ?int $limit = null): array
    {
        $dql = 'SELECT p FROM ' . Publisher::class . ' p WHERE p.publisherName LIKE :query ORDER BY p.publisherName';
        $queryObj = $this->entityManager->createQuery($dql);
        $queryObj->setParameter('query', $query . '%');
        if ($limit) {
            $queryObj->setMaxResults($limit);
        }
        return $queryObj->getResult();
    }
}
