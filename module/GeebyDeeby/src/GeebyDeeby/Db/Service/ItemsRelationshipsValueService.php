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
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationship;
use GeebyDeeby\Db\Entity\ItemsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationshipsValue;
use GeebyDeeby\Db\Entity\ItemsRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
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
     * @param EntityManager            $entityManager            Entity manager
     * @param PersistenceManager       $persistenceManager       Persistence manager
     * @param ItemsRelationshipService $itemsRelationshipService ItemsRelationships database service
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Service\PluginManager::class)]
        protected ItemsRelationshipService $itemsRelationshipService
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
        $entity = new ItemsRelationshipsValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, r.id AS Items_Relationship_ID '
            . 'FROM ' . ItemsRelationshipsValue::class
            . ' rv INNER JOIN ' . Item::class . ' i ON rv.subjectItem=i.id '
            . 'INNER JOIN ' . ItemsRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.objectItem=:item ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        $dql = 'SELECT i.id AS Item_ID, i.itemName AS Item_Name, r.id AS Items_Relationship_ID '
            . 'FROM ' . ItemsRelationshipsValue::class
            . ' rv INNER JOIN ' . Item::class . ' i ON rv.objectItem=i.id '
            . 'INNER JOIN ' . ItemsRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.subjectItem=:item ORDER BY i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
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
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getItemsRelatedtoSubjectItem($itemID);
        foreach ($subjectList as $current) {
            $index[$current['Items_Relationship_ID']][] = $current;
        }
        $objectList = $this->getItemsRelatedtoObjectItem($itemID);
        foreach ($objectList as $current) {
            $index['i' . $current['Items_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->itemsRelationshipService->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
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
        $params = [
            'subject' => $subject instanceof ItemEntityInterface ? $subject->getId() : $subject,
            'object' => $object instanceof ItemEntityInterface ? $object->getId() : $object,
            'relationship' => $relationship instanceof ItemsRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        $dql = 'SELECT r FROM ' . ItemsRelationshipsValue::class
            . ' r WHERE r.subjectItem=:subject AND r.objectItem=:object AND r.relationship=:relationship';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
