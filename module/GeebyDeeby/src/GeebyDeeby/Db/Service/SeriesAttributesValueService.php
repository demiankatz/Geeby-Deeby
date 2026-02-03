<?php

/**
 * Database service for the Series_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\SeriesAttributesValueEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesAttributesValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesAttributesValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager     $persistenceManager Persistence manager
     * @param SeriesAttributesValues $valuesTable        SeriesAttribute table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesAttributesValues $valuesTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesAttributesValueEntityInterface
     */
    public function createEntity(): SeriesAttributesValueEntityInterface
    {
        return $this->valuesTable->createRow();
    }

    /**
     * Get a list of attributes for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getAttributesForSeries(int $seriesID): array
    {
        return iterator_to_array($this->valuesTable->getAttributesForSeries($seriesID));
    }

    /**
     * Delete existing attributes associated with the provided series.
     *
     * @param int|SeriesEntityInterface $series Series entity or ID
     *
     * @return void
     */
    public function deleteBySeries(int|SeriesEntityInterface $series): void
    {
        $where = ['Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series];
        $this->valuesTable->delete($where);
    }
}
