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

use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesBibliography;
use GeebyDeeby\Db\Entity\SeriesBibliographyEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;

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
     * Create an empty entity.
     *
     * @return SeriesBibliographyEntityInterface
     */
    public function createEntity(): SeriesBibliographyEntityInterface
    {
        $entity = new SeriesBibliography();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, mt.id AS Material_Type_ID, '
            . 'mt.singularName AS Material_Type_Name, mt.pluralName AS Material_Type_Plural_Name '
            . 'FROM ' . SeriesBibliography::class . ' b '
            . 'INNER JOIN ' . Item::class . ' i ON b.item=i.id '
            . 'INNER JOIN ' . MaterialType::class . ' mt ON i.materialType=mt.id '
            . 'WHERE b.series = :series ORDER BY mt.singularName, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
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
        $dql = 'SELECT s.id AS Series_ID, s.seriesName AS Series_Name '
            . 'FROM ' . SeriesBibliography::class . ' b '
            . 'INNER JOIN ' . Series::class . ' s ON b.series=s.id '
            . 'WHERE b.item = :item ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        $params = [
            'series' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT b FROM ' . SeriesBibliography::class . ' b WHERE b.item = :item AND b.series = :series';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
