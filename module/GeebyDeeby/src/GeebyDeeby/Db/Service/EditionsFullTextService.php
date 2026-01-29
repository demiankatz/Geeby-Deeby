<?php

/**
 * Database service for the Editions_Full_Text table.
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

use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;
use GeebyDeeby\Db\Table\EditionsFullText;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Full_Text table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EditionsFullText $editionsFullTextTable EditionsFullText table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsFullText $editionsFullTextTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsFullTextEntityInterface
     */
    public function createEntity(): EditionsFullTextEntityInterface
    {
        return $this->editionsFullTextTable->createRow();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?EditionsFullTextEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsFullTextEntityInterface
    {
        return $this->editionsFullTextTable->getByPrimaryKey($id);
    }

    /**
     * Get a list of full text links for a particular edition.
     *
     * @param int $edition Edition ID
     *
     * @return array
     */
    public function getFullTextForEdition(int $edition): array
    {
        return iterator_to_array($this->editionsFullTextTable->getFullTextForEdition($edition));
    }

    /**
     * Get a list of full text links for a particular edition (or its immediate
     * parent).
     *
     * @param int $edition Edition ID
     *
     * @return array
     */
    public function getFullTextForEditionOrParentEdition(int $edition): array
    {
        return iterator_to_array($this->editionsFullTextTable->getFullTextForEditionOrParentEdition($edition));
    }

    /**
     * Get a list of full text links for a particular item.
     *
     * @param int $item Item ID
     *
     * @return array
     */
    public function getFullTextForItem(int $item): array
    {
        return iterator_to_array($this->editionsFullTextTable->getFullTextForItem($item));
    }

    /**
     * Get a list of items with full text.
     *
     * @param ?int $series Series ID (optional limiter)
     * @param bool $fuzzy  Use fuzzy matching? (default = false)
     * @param ?int $source Full text source ID (optional limiter)
     *
     * @return array
     */
    public function getItemsWithFullText(
        ?int $series = null,
        bool $fuzzy = false,
        ?int $source = null
    ): array {
        return iterator_to_array($this->editionsFullTextTable->getItemsWithFullText($series, $fuzzy, $source));
    }
}
