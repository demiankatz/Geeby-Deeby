<?php

/**
 * Database service for the Editions_Images table.
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

use GeebyDeeby\Db\Entity\EditionsImageEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsImages;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Images table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsImageService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager $persistenceManager  Persistence manager
     * @param EditionsImages     $editionsImagesTable EditionsImages table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsImages $editionsImagesTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsImageEntityInterface
     */
    public function createEntity(): EditionsImageEntityInterface
    {
        return $this->editionsImagesTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?EditionsImageEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsImageEntityInterface
    {
        return $this->editionsImagesTable->getByPrimaryKey($id);
    }

    /**
     * Get a list of thumbnails attached to multiple editions.
     *
     * @return array
     */
    public function getDuplicateThumbs(): array
    {
        return iterator_to_array($this->editionsImagesTable->getDuplicateThumbs());
    }

    /**
     * Get edition information matching a particular thumbnail path.
     *
     * @param string $thumb Thumb path
     *
     * @return array
     */
    public function getEditionsForThumb(string $thumb): array
    {
        return iterator_to_array($this->editionsImagesTable->getEditionsForThumb($thumb));
    }

    /**
     * Get a list of images for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsImageEntityInterface[]
     */
    public function getImagesForEdition(int $editionID): array
    {
        $callback = function ($select) use ($editionID): void {
            $select->order(['Editions_Images.Position']);
            $select->where->equalTo('Edition_ID', $editionID);
        };
        return iterator_to_array($this->editionsImagesTable->select($callback));
    }

    /**
     * Get a list of images for the specified edition (or its immediate parent).
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getImagesForEditionOrParentEdition(int $editionID): array
    {
        return iterator_to_array($this->editionsImagesTable->getImagesForEditionOrParentEdition($editionID));
    }

    /**
     * Get a list of images for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getImagesForItem(int $itemID): array
    {
        return iterator_to_array($this->editionsImagesTable->getImagesForItem($itemID));
    }

    /**
     * Get image information for the specified series.
     *
     * @param int  $seriesID        Series ID
     * @param bool $groupByMaterial Should we group results by material type?
     *
     * @return array
     */
    public function getImagesForSeries(int $seriesID, bool $groupByMaterial = true): array
    {
        return iterator_to_array($this->editionsImagesTable->getImagesForSeries($seriesID, $groupByMaterial));
    }
}
