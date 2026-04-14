<?php

/**
 * Database service for the Series_Relationships_Values table.
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
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationship;
use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationshipsValue;
use GeebyDeeby\Db\Entity\SeriesRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Relationships_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesRelationshipsValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager             $entityManager             Entity manager
     * @param PersistenceManager        $persistenceManager        Persistence manager
     * @param SeriesRelationshipService $seriesRelationshipService SeriesRelationship database service
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Service\PluginManager::class)]
        protected SeriesRelationshipService $seriesRelationshipService
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesRelationshipsValueEntityInterface
     */
    public function createEntity(): SeriesRelationshipsValueEntityInterface
    {
        $entity = new SeriesRelationshipsValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of series related to the provided subject series ID.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getSeriesRelatedtoObjectSeries(int $seriesID): array
    {
        $dql = 'SELECT s.id AS Series_ID, s.seriesName AS Series_Name, r.id AS Series_Relationship_ID '
            . 'FROM ' . SeriesRelationshipsValue::class
            . ' rv INNER JOIN ' . Series::class . ' s ON rv.subjectSeries=s.id '
            . 'INNER JOIN ' . SeriesRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.objectSeries=:series ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Get a list of series related to the provided subject series ID.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getSeriesRelatedtoSubjectSeries(int $seriesID): array
    {
        $dql = 'SELECT s.id AS Series_ID, s.seriesName AS Series_Name, r.id AS Series_Relationship_ID '
            . 'FROM ' . SeriesRelationshipsValue::class
            . ' rv INNER JOIN ' . Series::class . ' s ON rv.objectSeries=s.id '
            . 'INNER JOIN ' . SeriesRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.subjectSeries=:series ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Get a list of relationships for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getRelationshipsForSeries(int $seriesID): array
    {
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getSeriesRelatedtoSubjectSeries($seriesID);
        foreach ($subjectList as $current) {
            $index[$current['Series_Relationship_ID']][] = $current;
        }
        $objectList = $this->getSeriesRelatedtoObjectSeries($seriesID);
        foreach ($objectList as $current) {
            $index['i' . $current['Series_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->seriesRelationshipService->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
    }

    /**
     * Get a row matching the provided source container/contained series pair.
     *
     * @param int|SeriesEntityInterface             $subject      Subject series ID or entity
     * @param int|SeriesEntityInterface             $object       Object series ID or entity
     * @param int|SeriesRelationshipEntityInterface $relationship Relationship ID or entity
     *
     * @return ?SeriesRelationshipsValueEntityInterface
     */
    public function getBySubjectAndObjectAndRelationship(
        int|SeriesEntityInterface $subject,
        int|SeriesEntityInterface $object,
        int|SeriesRelationshipEntityInterface $relationship
    ): ?SeriesRelationshipsValueEntityInterface {
        $params = [
            'subject' => $subject instanceof SeriesEntityInterface ? $subject->getId() : $subject,
            'object' => $object instanceof SeriesEntityInterface ? $object->getId() : $object,
            'relationship' => $relationship instanceof SeriesRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        $dql = 'SELECT r FROM ' . SeriesRelationshipsValue::class
            . ' r WHERE r.subjectSeries=:subject AND r.objectSeries=:object AND r.relationship=:relationship';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
