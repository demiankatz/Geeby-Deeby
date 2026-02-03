<?php

/**
 * Database service for the Editions_ISBNs table.
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

use GeebyDeeby\Db\Entity\EditionsIsbnEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\EditionsISBNs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_ISBNs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsIsbnService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param EditionsISBNs      $isbnsTable         EditionsISBNs table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsISBNs $isbnsTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsIsbnEntityInterface
     */
    public function createEntity(): EditionsIsbnEntityInterface
    {
        return $this->isbnsTable->createRow();
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsIsbnEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsIsbnEntityInterface
    {
        return $this->isbnsTable->getByPrimaryKey($id) ?: null;
    }

    /**
     * Get a list of ISBNs for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getISBNsForEdition(int $editionID): array
    {
        return iterator_to_array($this->isbnsTable->getISBNsForEdition($editionID));
    }

    /**
     * Get a list of ISBNs for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getISBNsForItem(int $itemID): array
    {
        return iterator_to_array($this->isbnsTable->getISBNsForItem($itemID));
    }

    /**
     * Find items matching an ISBN search query.
     *
     * @param string $q Query
     *
     * @return array
     */
    public function searchForItems(string $q): array
    {
        return iterator_to_array($this->isbnsTable->searchForItems($q));
    }
}
