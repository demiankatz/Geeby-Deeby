<?php

/**
 * Row Definition for Tags_Attributes_Values
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

use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsAttributeEntityInterface;
use GeebyDeeby\Db\Entity\TagsAttributesValueEntityInterface;

/**
 * Row Definition for Tags_Attributes_Values
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsAttributesValues extends TableAwareGateway implements TagsAttributesValueEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Tag_ID', 'Tags_Attribute_ID'], 'Tags_Attributes_Values', $adapter);
    }

    /**
     * Get associated tag.
     *
     * @return TagEntityInterface
     */
    public function getTag(): TagEntityInterface
    {
        return $this->getTableManager()->get('tag')->getByPrimaryKey($this->Tag_ID);
    }

    /**
     * Set associated tag.
     *
     * @param int|TagEntityInterface $tag Associated tag entity or ID
     *
     * @return static
     */
    public function setTag(int|TagEntityInterface $tag): static
    {
        $this->Tag_ID = $tag instanceof TagEntityInterface ? $tag->getId() : $tag;
        return $this;
    }

    /**
     * Get associated attribute.
     *
     * @return TagsAttributeEntityInterface
     */
    public function getAttribute(): TagsAttributeEntityInterface
    {
        return $this->getTableManager()->get('tagsattributes')->getByPrimaryKey($this->Tags_Attribute_ID);
    }

    /**
     * Set associated attribute.
     *
     * @param int|TagsAttributeEntityInterface $attribute Associated attribute entity or ID
     *
     * @return static
     */
    public function setAttribute(int|TagsAttributeEntityInterface $attribute): static
    {
        $this->Tags_Attribute_ID = $attribute instanceof TagsAttributeEntityInterface
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
        return $this->Tags_Attribute_Value;
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
        $this->Tags_Attribute_Value = $value;
        return $this;
    }
}
