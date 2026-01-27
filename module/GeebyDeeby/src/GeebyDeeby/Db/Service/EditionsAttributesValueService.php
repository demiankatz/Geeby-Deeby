<?php

/**
 * Database service for the Editions_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsAttributesValueEntityInterface;
use GeebyDeeby\Db\Table\EditionsAttributesValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsAttributesValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EditionsAttributesValues $valuesTable EditionsAttribute table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsAttributesValues $valuesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsAttributesValueEntityInterface
     */
    public function createEntity(): EditionsAttributesValueEntityInterface
    {
        return $this->valuesTable->createRow();
    }

    /**
     * Get a list of attributes for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getAttributesForEdition(int $editionID): array
    {
        return iterator_to_array($this->valuesTable->getAttributesForEdition($editionID));
    }

    /**
     * Get a list of attributes for the specified item.
     *
     * @param int|int[] $itemID      Item ID (or array of IDs)
     * @param ?int      $attributeID Attribute ID filter (null for all attributes)
     *
     * @return array
     */
    public function getAttributesForItem(int|array $itemID, ?int $attributeID = null): array
    {
        return iterator_to_array($this->valuesTable->getAttributesForItem($itemID, $attributeID));
    }

    /**
     * Delete existing attributes associated with the provided edition.
     *
     * @param int|EditionEntityInterface $edition Edition entity or ID
     *
     * @return void
     */
    public function deleteByEdition(int|EditionEntityInterface $edition): void
    {
        $where = ['Edition_ID' => $edition instanceof EditionEntityInterface ? $edition->getId() : $edition];
        $this->valuesTable->delete($where);
    }
}
