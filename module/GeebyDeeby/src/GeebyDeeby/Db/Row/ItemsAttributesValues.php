<?php

/**
 * Row Definition for Items_Attributes_Values
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
use GeebyDeeby\Db\Entity\ItemsAttributeEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAttributesValueEntityInterface;

/**
 * Row Definition for Items_Attributes_Values
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsAttributesValues extends TableAwareGateway implements ItemsAttributesValueEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Item_ID', 'Items_Attribute_ID'], 'Items_Attributes_Values', $adapter);
    }

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->getTableManager()->get('item')->getByPrimaryKey($this->Item_ID);
    }

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static
    {
        $this->Item_ID = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        return $this;
    }

    /**
     * Get associated attribute.
     *
     * @return ItemsAttributeEntityInterface
     */
    public function getAttribute(): ItemsAttributeEntityInterface
    {
        return $this->getTableManager()->get('itemsattributes')->getByPrimaryKey($this->Items_Attribute_ID);
    }

    /**
     * Set associated attribute.
     *
     * @param int|ItemsAttributeEntityInterface $attribute Associated attribute entity or ID
     *
     * @return static
     */
    public function setAttribute(int|ItemsAttributeEntityInterface $attribute): static
    {
        $this->Items_Attribute_ID = $attribute instanceof ItemsAttributeEntityInterface
            ? $attribute->getId() : $attribute;
        return $this;
    }

    /**
     * Get the value of the attribute.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->Items_Attribute_Value;
    }

    /**
     * Set the value of the attribute.
     *
     * @param string $value New value
     *
     * @return static
     */
    public function setValue(string $value): static
    {
        $this->Items_Attribute_Value = $value;
        return $this;
    }
}
