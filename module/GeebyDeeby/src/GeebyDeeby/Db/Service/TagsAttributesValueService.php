<?php

/**
 * Database service for the Tags_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsAttributesValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\TagsAttributesValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Tags_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsAttributesValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PersistenceManager   $persistenceManager Persistence manager
     * @param TagsAttributesValues $valuesTable        TagsAttribute table
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected TagsAttributesValues $valuesTable
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return TagsAttributesValueEntityInterface
     */
    public function createEntity(): TagsAttributesValueEntityInterface
    {
        return $this->valuesTable->createRow();
    }

    /**
     * Get a list of attributes for the specified tag.
     *
     * @param int $tagID Tag ID
     *
     * @return array
     */
    public function getAttributesForTag(int $tagID): array
    {
        return iterator_to_array($this->valuesTable->getAttributesForTag($tagID));
    }

    /**
     * Delete existing attributes associated with the provided tag.
     *
     * @param int|TagEntityInterface $tag Tag entity or ID
     *
     * @return void
     */
    public function deleteByTag(int|TagEntityInterface $tag): void
    {
        $where = ['Tag_ID' => $tag instanceof TagEntityInterface ? $tag->getId() : $tag];
        $this->valuesTable->delete($where);
    }
}
