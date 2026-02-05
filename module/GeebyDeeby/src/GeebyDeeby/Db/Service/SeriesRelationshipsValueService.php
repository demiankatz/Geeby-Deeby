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

use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesRelationshipsValues;
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
     * @param PersistenceManager        $persistenceManager      Persistence manager
     * @param SeriesRelationshipsValues $relationshipsValueTable SeriesRelationshipsValues table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesRelationshipsValues $relationshipsValueTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesRelationshipsValueEntityInterface
     */
    public function createEntity(): SeriesRelationshipsValueEntityInterface
    {
        return $this->relationshipsValueTable->createRow();
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
        return iterator_to_array($this->relationshipsValueTable->getSeriesRelatedtoObjectSeries($seriesID));
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
        return iterator_to_array($this->relationshipsValueTable->getSeriesRelatedtoSubjectSeries($seriesID));
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
        return $this->relationshipsValueTable->getRelationshipsForSeries($seriesID);
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
        $where = [
            'Subject_Series_ID' => $subject instanceof SeriesEntityInterface ? $subject->getId() : $subject,
            'Object_Series_ID' => $object instanceof SeriesEntityInterface ? $object->getId() : $object,
            'Series_Relationship_ID' => $relationship instanceof SeriesRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        foreach ($this->relationshipsValueTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
