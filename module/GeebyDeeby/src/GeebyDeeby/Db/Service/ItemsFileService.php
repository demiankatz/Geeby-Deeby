<?php

/**
 * Database service for the Items_Files table.
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

use GeebyDeeby\Db\Entity\File;
use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\FileType;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsFile;
use GeebyDeeby\Db\Entity\ItemsFileEntityInterface;

/**
 * Database service for the Items_Files table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsFileService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsFileEntityInterface
     */
    public function createEntity(): ItemsFileEntityInterface
    {
        $entity = new ItemsFile();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of items for the specified file.
     *
     * @param int $fileID File ID
     *
     * @return ItemEntityInterface[]
     */
    public function getItemsForFile(int $fileID): array
    {
        $dql = 'SELECT i FROM ' . ItemsFile::class . ' if INNER JOIN ' . Item::class . ' i ON if.item=i.id '
            . 'WHERE if.file = :file ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('file', $fileID);
        return $query->getResult();
    }

    /**
     * Get a list of files for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getFilesForItem(int $itemID): array
    {
        $dql = 'SELECT ft.id AS File_Type_ID, ft.fileTypeName AS File_Type, '
            . 'f.id AS File_ID, f.fileName AS File_Name, f.path AS File_Path, f.description AS Description '
            . 'FROM ' . ItemsFile::class . ' if INNER JOIN ' . File::class . ' f ON if.file=f.id '
            . 'INNER JOIN ' . FileType::class . ' ft ON f.fileType=ft.id '
            . 'WHERE if.item = :item ORDER BY ft.fileTypeName, f.fileName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Retrieve the record for the specified file and item.
     *
     * @param int|FileEntityInterface $file File ID or entity
     * @param int|ItemEntityInterface $item Item ID or entity
     *
     * @return ?ItemsFileEntityInterface
     */
    public function getForFileAndItem(
        int|FileEntityInterface $file,
        int|ItemEntityInterface $item
    ): ?ItemsFileEntityInterface {
        $params = [
            'file' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'item' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        $dql = 'SELECT if FROM ' . ItemsFile::class . ' if WHERE if.item = :item AND if.file = :file';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
