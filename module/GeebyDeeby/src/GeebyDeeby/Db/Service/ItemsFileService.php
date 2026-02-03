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

use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsFileEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsFiles;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param ItemsFiles         $itemsFilesTable    ItemsFiles table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsFiles $itemsFilesTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsFileEntityInterface
     */
    public function createEntity(): ItemsFileEntityInterface
    {
        return $this->itemsFilesTable->createRow();
    }

    /**
     * Get a list of items for the specified file.
     *
     * @param int $fileID File ID
     *
     * @return array
     */
    public function getItemsForFile(int $fileID): array
    {
        return iterator_to_array($this->itemsFilesTable->getItemsForFile($fileID));
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
        return iterator_to_array($this->itemsFilesTable->getFilesForItem($itemID));
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
        $where = [
            'File_ID' => $file instanceof FileEntityInterface ? $file->getId() : $file,
            'Item_ID' => $item instanceof ItemEntityInterface ? $item->getId() : $item,
        ];
        foreach ($this->itemsFilesTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
