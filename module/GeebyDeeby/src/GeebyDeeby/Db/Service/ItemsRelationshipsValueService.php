<?php

/**
 * Database service for the Items_Relationships_Values table.
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
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\ItemsRelationshipsValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Items_Relationships_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsRelationshipsValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager            $entityManager           Entity manager
     * @param PersistenceManager       $persistenceManager      Persistence manager
     * @param ItemsRelationshipsValues $relationshipsValueTable ItemsRelationshipsValues table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected ItemsRelationshipsValues $relationshipsValueTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return ItemsRelationshipsValueEntityInterface
     */
    public function createEntity(): ItemsRelationshipsValueEntityInterface
    {
        return $this->relationshipsValueTable->createRow();
    }

    /**
     * Get a list of items related to the provided subject item ID.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemsRelatedtoObjectItem(int $itemID): array
    {
        return iterator_to_array($this->relationshipsValueTable->getItemsRelatedtoObjectItem($itemID));
    }

    /**
     * Get a list of items related to the provided subject item ID.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getItemsRelatedtoSubjectItem(int $itemID): array
    {
        return iterator_to_array($this->relationshipsValueTable->getItemsRelatedtoSubjectItem($itemID));
    }

    /**
     * Get a list of relationships for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getRelationshipsForItem(int $itemID): array
    {
        return $this->relationshipsValueTable->getRelationshipsForItem($itemID);
    }

    /**
     * Get a row matching the provided source container/contained item pair.
     *
     * @param int|ItemEntityInterface              $subject      Subject item ID or entity
     * @param int|ItemEntityInterface              $object       Object item ID or entity
     * @param int|ItemsRelationshipEntityInterface $relationship Relationship ID or entity
     *
     * @return ?ItemsRelationshipsValueEntityInterface
     */
    public function getBySubjectAndObjectAndRelationship(
        int|ItemEntityInterface $subject,
        int|ItemEntityInterface $object,
        int|ItemsRelationshipEntityInterface $relationship
    ): ?ItemsRelationshipsValueEntityInterface {
        $where = [
            'Subject_Item_ID' => $subject instanceof ItemEntityInterface ? $subject->getId() : $subject,
            'Object_Item_ID' => $object instanceof ItemEntityInterface ? $object->getId() : $object,
            'Items_Relationship_ID' => $relationship instanceof ItemsRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        foreach ($this->relationshipsValueTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
