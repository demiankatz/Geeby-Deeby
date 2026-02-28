<?php

/**
 * Database service for the Items_Creators_Citations table.
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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\CitationEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorsCitationEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsCreatorsCitations;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Creators_Citations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreatorsCitationService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager          $entityManager      Entity manager
     * @param PersistenceManager     $persistenceManager Persistence manager
     * @param ItemsCreatorsCitations $itemsCreatorsTable ItemsCreatorsCitations table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsCreatorsCitations $itemsCreatorsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsCreatorsCitationEntityInterface
     */
    public function createEntity(): ItemsCreatorsCitationEntityInterface
    {
        return $this->itemsCreatorsTable->createRow();
    }

    /**
     * Given a row ID from Items_Creators, return a list of citations
     *
     * @param int $rowID Row ID
     *
     * @return array
     */
    public function getCitations(int $rowID): array
    {
        return iterator_to_array($this->itemsCreatorsTable->getCitations($rowID));
    }

    /**
     * Get an entity by item, person and role.
     *
     * @param int|ItemsCreatorEntityInterface $creator  Creator entity or ID
     * @param int|CitationEntityInterface     $citation Citation entity or ID
     *
     * @return ?ItemsCreatorsCitationEntityInterface
     */
    public function getByCreatorAndCitation(
        int|ItemsCreatorEntityInterface $creator,
        int|CitationEntityInterface $citation
    ): ?ItemsCreatorsCitationEntityInterface {
        $where = [
            'Item_Creator_ID' => $creator instanceof ItemsCreatorEntityInterface ? $creator->getId() : $creator,
            'Citation_ID' => $citation instanceof CitationEntityInterface ? $citation->getId() : $citation,
        ];
        foreach ($this->itemsCreatorsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
