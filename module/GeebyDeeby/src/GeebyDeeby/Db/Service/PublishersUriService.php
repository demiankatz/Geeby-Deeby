<?php

/**
 * Database service for the Publishers_URIs table.
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
use GeebyDeeby\Db\Entity\Predicate;
use GeebyDeeby\Db\Entity\Publisher;
use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Entity\PublishersUri;
use GeebyDeeby\Db\Entity\PublishersUriEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Publishers_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersUriService extends AbstractDbService
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
     * @return PublishersUriEntityInterface
     */
    public function createEntity(): PublishersUriEntityInterface
    {
        $entity = new PublishersUri();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of publishers for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getPublishersForURI(string $uri): array
    {
        $dql = 'SELECT pu.id AS Sequence_ID, pu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev, '
            . 'pub.id AS Publisher_ID, pub.publisherName as Publisher_Name'
            . ' FROM ' . PublishersUri::class . ' pu INNER JOIN ' . Predicate::class . ' p ON pu.predicate = p.id'
            . ' INNER JOIN ' . Publisher::class . ' pub ON pu.publisher = pub.id'
            . ' WHERE pu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('uri', $uri);
        return $query->getResult();
    }

    /**
     * Get a list of URIs for the specified publisher.
     *
     * @param int|PublisherEntityInterface $publisher Publisher entity or ID
     *
     * @return array
     */
    public function getURIsForPublisher(int|PublisherEntityInterface $publisher): array
    {
        $dql = 'SELECT pu.id AS Sequence_ID, pu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev '
            . ' FROM ' . PublishersUri::class . ' pu INNER JOIN ' . Predicate::class . ' p ON pu.predicate = p.id'
            . ' WHERE pu.publisher = :publisher ORDER BY pu.uri, p.abbreviation';
        $query = $this->entityManager->createQuery($dql);
        $publisherId = $publisher instanceof PublisherEntityInterface ? $publisher->getId() : $publisher;
        $query->setParameter('publisher', $publisherId);
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using a publisher ID and URI (null if not found).
     *
     * @param int    $publisherId Publisher ID
     * @param string $uri         URI
     *
     * @return ?PublishersUriEntityInterface
     */
    public function getByPublisherAndUri(int $publisherId, string $uri): ?PublishersUriEntityInterface
    {
        $dql = 'SELECT pu FROM ' . PublishersUri::class . ' pu WHERE pu.publisher = :publisher AND pu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['publisher' => $publisherId, 'uri' => $uri]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
