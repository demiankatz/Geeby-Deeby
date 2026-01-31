<?php

/**
 * Database service for the Editions_Full_Text_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\EditionsFullTextAttributesValueEntityInterface;
use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;
use GeebyDeeby\Db\Table\EditionsFullTextAttributesValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Editions_Full_Text_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextAttributesValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EditionsFullTextAttributesValues $valuesTable EditionsFullTextAttribute table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsFullTextAttributesValues $valuesTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsFullTextAttributesValueEntityInterface
     */
    public function createEntity(): EditionsFullTextAttributesValueEntityInterface
    {
        return $this->valuesTable->createRow();
    }

    /**
     * Get a list of attributes for the specified full text ID(s).
     *
     * @param int|int[] $fullTextID Full text ID (or array of IDs)
     *
     * @return mixed
     */
    public function getAttributesForFullTextIDs(int|array $fullTextID)
    {
        return iterator_to_array($this->valuesTable->getAttributesForFullTextIDs((array)$fullTextID));
    }

    /**
     * Delete existing attributes associated with the provided edition full text entry.
     *
     * @param int|EditionsFullTextEntityInterface $eft Edition full text entity or ID
     *
     * @return void
     */
    public function deleteByEditionFullText(int|EditionsFullTextEntityInterface $eft): void
    {
        $where = ['Editions_Full_Text_ID' => $eft instanceof EditionsFullTextEntityInterface ? $eft->getId() : $eft];
        $this->valuesTable->delete($where);
    }
}
