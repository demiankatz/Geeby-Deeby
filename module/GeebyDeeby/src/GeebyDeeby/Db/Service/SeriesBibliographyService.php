<?php

/**
 * Database service for the Series_Bibliography table.
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
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\SeriesBibliographyEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesBibliography;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Bibliography table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesBibliographyService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager           Entity manager
     * @param PersistenceManager $persistenceManager      Persistence manager
     * @param SeriesBibliography $seriesBibliographyTable SeriesBibliography table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesBibliography $seriesBibliographyTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesBibliographyEntityInterface
     */
    public function createEntity(): SeriesBibliographyEntityInterface
    {
        return $this->seriesBibliographyTable->createRow();
    }

    /**
     * Get a list of items describing the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getItemsDescribingSeries(int $seriesID): array
    {
        return iterator_to_array($this->seriesBibliographyTable->getItemsDescribingSeries($seriesID));
    }

    /**
     * Get a list of series described by the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getSeriesDescribedByItem(int $itemID): array
    {
        return iterator_to_array($this->seriesBibliographyTable->getSeriesDescribedByItem($itemID));
    }

    /**
     * Retrieve the entity for a particular bibliography entry.
     *
     * @param int|ItemEntityInterface   $item   Bibliography Item ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     *
     * @return ?ItemsBibliographyEntityInterface
     */
    public function getByItemAndSeries(
        int|ItemEntityInterface $item,
        int|SeriesEntityInterface $series
    ): ?SeriesBibliographyEntityInterface {
        $where = [
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        foreach ($this->seriesBibliographyTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
