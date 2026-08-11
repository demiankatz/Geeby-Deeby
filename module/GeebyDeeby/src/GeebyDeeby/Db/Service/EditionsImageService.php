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

use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionsImage;
use GeebyDeeby\Db\Entity\EditionsImageEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDate;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\MaterialType;
use GeebyDeeby\Db\Entity\Note;

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
     * Create an empty entity.
     *
     * @return EditionsImageEntityInterface
     */
    public function createEntity(): EditionsImageEntityInterface
    {
        $entity = new EditionsImage();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        return $this->entityManager->find(EditionsImage::class, $id);
    }

    /**
     * Get a list of thumbnails attached to multiple editions.
     *
     * @return array
     */
    public function getDuplicateThumbs(): array
    {
        $dql = 'SELECT i.thumbPath AS Thumb_Path, COUNT(i.thumbPath) AS c FROM '
            . EditionsImage::class . ' i GROUP BY i.thumbPath HAVING c > 1';
        $query = $this->entityManager->createQuery($dql);
        return $query->getResult();
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
        $dql = 'SELECT DISTINCT e.id AS Edition_ID, i.id AS Sequence_ID, e.editionName AS Edition_Name FROM '
            . EditionsImage::class . ' i '
            . 'INNER JOIN ' . Edition::class . ' e ON i.edition=e.id '
            . 'WHERE i.thumbPath=:path';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('path', $thumb);
        return $query->getResult();
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
        $dql = 'SELECT i FROM ' . EditionsImage::class . ' i WHERE i.edition=:edition ORDER BY i.position';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
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
        $dql = 'SELECT i.id AS Sequence_ID, e.id AS Edition_ID, i.imagePath AS Image_Path, i.thumbPath AS Thumb_Path, '
            . 'i.iiifUri AS IIIF_URI, i.position AS Position, n.id AS Note_ID, n.note AS Note '
            . 'FROM ' . EditionsImage::class . ' i '
            . 'INNER JOIN ' . Edition::class . ' e ON i.edition=e.id OR i.edition=e.parentEdition '
            . 'LEFT JOIN ' . Note::class . ' n ON i.note=n.id '
            . 'WHERE e.id=:edition ORDER BY i.position';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
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
        $dql = 'SELECT DISTINCT e.id AS Edition_ID, i.imagePath AS Image_Path, i.thumbPath AS Thumb_Path, '
            . 'i.iiifUri AS IIIF_URI, i.position AS Position, n.id AS Note_ID, n.note AS Note, item.id AS Item_ID, '
            . 'MIN(erd.year) AS Earliest_Year '
            . 'FROM ' . EditionsImage::class . ' i '
            . 'INNER JOIN ' . Edition::class . ' e ON i.edition=e.id OR i.edition=e.parentEdition '
            . 'INNER JOIN ' . Item::class . ' item ON e.item=item.id '
            . 'LEFT JOIN ' . EditionsReleaseDate::class . ' erd ON e.id=erd.edition OR e.parentEdition=erd.edition '
            . 'LEFT JOIN ' . Note::class . ' n ON i.note=n.id '
            . 'WHERE item.id=:item '
            . 'GROUP BY e.id, i.imagePath, i.thumbPath, i.iiifUri, i.position, n.id, e.editionName, item.id, n.note '
            . 'ORDER BY e.itemDisplayOrder, i.position, Earliest_Year';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        $order = 'e.volume, e.position, e.replacementNumber, e.itemDisplayOrder, item.itemName, i.position';
        if ($groupByMaterial) {
            $order = "mt.singularName, $order";
        }
        $dql = 'SELECT i.thumbPath AS Thumb_Path, i.iiifUri AS IIIF_URI, item.id AS Item_ID, '
            . 'mt.id AS Material_Type_ID, mt.singularName AS Material_Type_Name, '
            . 'e.id AS Edition_ID, e.editionName AS Edition_Name, n.id AS Note_ID, n.note AS Note '
            . 'FROM ' . EditionsImage::class . ' i '
            . 'INNER JOIN ' . Edition::class . ' e ON i.edition=e.id '
            . 'INNER JOIN ' . Item::class . ' item ON e.item=item.id '
            . 'INNER JOIN ' . MaterialType::class . ' mt ON item.materialType=mt.id '
            . 'LEFT JOIN ' . Note::class . ' n ON i.note=n.id '
            . 'WHERE e.series=:series '
            . 'GROUP BY i.thumbPath, i.iiifUri, e.volume, e.position, e.replacementNumber, i.position, item.id, n.note '
            . 'ORDER BY ' . $order;
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Get image information where the link points to a specific domain (or other substring).
     *
     * @param string $domain String to search for in the Image_Path field
     *
     * @return EditionsImageEntityInterface[]
     */
    public function getByDomain(string $domain): array
    {
        $dql = 'SELECT i FROM ' . EditionsImage::class . ' i WHERE i.imagePath LIKE :domain';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('domain', "%$domain%");
        return $query->getResult();
    }
}
