<?php

/**
 * Database service for the Editions_Product_Codes table.
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

use GeebyDeeby\Db\Entity\EditionsProductCodeEntityInterface;
use GeebyDeeby\Db\Table\EditionsProductCodes;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Product_Codes table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsProductCodeService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EditionsProductCodes $productCodesTable EditionsProductCodes table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsProductCodes $productCodesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsProductCodeEntityInterface
     */
    public function createEntity(): EditionsProductCodeEntityInterface
    {
        return $this->productCodesTable->createRow();
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsProductCodeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsProductCodeEntityInterface
    {
        return $this->productCodesTable->getByPrimaryKey($id) ?: null;
    }

    /**
     * Get a list of OCLC Numbers for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getProductCodesForEdition(int $editionID): array
    {
        return iterator_to_array($this->productCodesTable->getProductCodesForEdition($editionID));
    }

    /**
     * Get a list of OCLC Numbers for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getProductCodesForItem(int $itemID): array
    {
        return iterator_to_array($this->productCodesTable->getProductCodesForItem($itemID));
    }
}
