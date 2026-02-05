<?php

/**
 * Row Definition for Items_Relationships_Values
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\ItemsRelationshipsValueEntityInterface;

/**
 * Row Definition for Items_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsRelationshipsValues extends TableAwareGateway implements ItemsRelationshipsValueEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(
            ['Subject_Item_ID', 'Items_Relationship_ID', 'Object_Item_ID'],
            'Items_Relationships_Values',
            $adapter
        );
    }

    /**
     * Get subject item.
     *
     * @return ItemEntityInterface
     */
    public function getSubject(): ItemEntityInterface
    {
        return $this->getTableManager()->get('item')->getByPrimaryKey($this->Subject_Item_ID);
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
        $this->Subject_Item_ID = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return ItemsRelationshipEntityInterface
     */
    public function getRelationship(): ItemsRelationshipEntityInterface
    {
        return $this->getTableManager()->get('itemsrelationship')->getByPrimaryKey($this->Items_Relationship_ID);
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
        $this->Items_Relationship_ID = $relationship instanceof ItemsRelationshipEntityInterface
            ? $relationship->getId() : $relationship;
        return $this;
    }

    /**
     * Get object item.
     *
     * @return ItemEntityInterface
     */
    public function getObject(): ItemEntityInterface
    {
        return $this->getTableManager()->get('item')->getByPrimaryKey($this->Object_Item_ID);
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
        $this->Object_Item_ID = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        return $this;
    }
}
