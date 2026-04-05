<?php

/**
 * Database service for the Series_Categories table.
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

use GeebyDeeby\Db\Entity\Category;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesCategory;
use GeebyDeeby\Db\Entity\SeriesCategoryEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;

/**
 * Database service for the Series_Categories table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesCategoryService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesCategoryEntityInterface
     */
    public function createEntity(): SeriesCategoryEntityInterface
    {
        $entity = new SeriesCategory();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get series for the specified tag.
     *
     * @param int $categoryID Category ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForCategory(int $categoryID): array
    {
        $dql = 'SELECT s FROM ' . SeriesCategory::class . ' sc '
            . 'INNER JOIN ' . Series::class . ' s ON sc.series=s.id '
            . 'WHERE sc.category=:category ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('category', $categoryID);
        return $query->getResult();
    }

    /**
     * Get a list of tags for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getCategoriesForSeries(int $seriesID): array
    {
        $dql = 'SELECT c FROM ' . SeriesCategory::class . ' sc '
            . 'INNER JOIN ' . Category::class . ' c ON sc.category=c.id '
            . 'WHERE sc.series=:series ORDER BY c.categoryName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Delete the categories for a series.
     *
     * @param int $seriesId Series ID
     *
     * @return void
     */
    public function deleteCategoriesForSeries(int $seriesId): void
    {
        $dql = 'DELETE FROM ' . SeriesCategory::class . ' sc WHERE sc.series=:series';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesId);
        $query->execute();
    }

    /**
     * Set the categories for a series (clearing any prior category data in the process).
     *
     * @param int   $seriesId   Series ID
     * @param int[] $categories Category IDs
     *
     * @return void
     */
    public function setCategoriesForSeries(int $seriesId, array $categories): void
    {
        $this->deleteCategoriesForSeries($seriesId);
        foreach ($categories as $cat) {
            $this->persistEntity($this->createEntity()->setSeries($seriesId)->setCategory($cat));
        }
    }
}
