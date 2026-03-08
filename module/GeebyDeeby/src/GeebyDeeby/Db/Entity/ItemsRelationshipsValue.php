<?php

/**
 * Item relationship value entity model.
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
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item relationship value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Relationships_Values')]
#[ORM\Index(name: 'idx_b7aca6875e7ffd94', columns: ['Subject_Item_ID'])]
#[ORM\Index(name: 'items_relationship_id', columns: ['Items_Relationship_ID'])]
#[ORM\Index(name: 'object_item_id', columns: ['Object_Item_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsRelationshipsValue extends AbstractEntity implements ItemsRelationshipsValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Subject item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Subject_Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $subjectItem;

    /**
     * Relationship.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Items_Relationship_ID',
        referencedColumnName: 'Items_Relationship_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ItemsRelationship::class)]
    protected ItemsRelationship $relationship;

    /**
     * Object item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Object_Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $objectItem;

    /**
     * Get subject item.
     *
     * @return ItemEntityInterface
     */
    public function getSubject(): ItemEntityInterface
    {
        return $this->subjectItem;
    }

    /**
     * Set subject item.
     *
     * @param int|ItemEntityInterface $item Subject item entity or ID
     *
     * @return static
     */
    public function setSubject(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->subjectItem = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->subjectItem = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return ItemsRelationshipEntityInterface
     */
    public function getRelationship(): ItemsRelationshipEntityInterface
    {
        return $this->relationship;
    }

    /**
     * Set relationship.
     *
     * @param int|ItemsRelationshipEntityInterface $relationship Relationship entity or ID
     *
     * @return static
     */
    public function setRelationship(int|ItemsRelationshipEntityInterface $relationship): static
    {
        if ($relationship instanceof ItemsRelationship) {
            $this->relationship = $relationship;
            return $this;
        } elseif ($relationship instanceof ItemsRelationshipEntityInterface) {
            $relationship = $relationship->getId();
        }
        $this->relationship = $this->entityManager->getReference(ItemsRelationship::class, $relationship);
        return $this;
    }

    /**
     * Get object item.
     *
     * @return ItemEntityInterface
     */
    public function getObject(): ItemEntityInterface
    {
        return $this->objectItem;
    }

    /**
     * Set object item.
     *
     * @param int|ItemEntityInterface $item Object item entity or ID
     *
     * @return static
     */
    public function setObject(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->objectItem = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->objectItem = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }
}
