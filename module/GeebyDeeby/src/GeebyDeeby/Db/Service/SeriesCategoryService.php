<?php

/**
 * Database service for the Series_Categories table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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

use GeebyDeeby\Db\Entity\SeriesCategoryEntityInterface;
use GeebyDeeby\Db\Table\SeriesCategories;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param SeriesCategories $seriesCategoriesTable SeriesCategories table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesCategories $seriesCategoriesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesCategoryEntityInterface
     */
    public function createEntity(): SeriesCategoryEntityInterface
    {
        return $this->seriesCategoriesTable->createRow();
    }

    /**
     * Get series for the specified tag.
     *
     * @param int    $tagID Category ID
     * @param string $sort  Sort type (title or series; default = title)
     *
     * @return array
     */
    public function getSeriesForCategory(int $tagID, string $sort = 'title'): array
    {
        return iterator_to_array($this->seriesCategoriesTable->getSeriesForCategory($tagID));
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
        return iterator_to_array($this->seriesCategoriesTable->getCategories($seriesID));
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
        $this->seriesCategoriesTable->delete(['Series_ID' => $seriesId]);
        foreach ($categories as $cat) {
            $this->seriesCategoriesTable->insert(['Series_ID' => $seriesId, 'Category_ID' => $cat]);
        }
    }
}
