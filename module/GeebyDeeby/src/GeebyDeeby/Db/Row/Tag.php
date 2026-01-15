<?php

/**
 * Row Definition for Tags
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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
use GeebyDeeby\Db\Entity\TagTypeEntityInterface;

/**
 * Row Definition for Tags
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Tag extends TableAwareGateway implements TagEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Tag_ID', 'Tags', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Tag_ID;
    }

    /**
     * Get the tag text.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->Tag;
    }

    /**
     * Set the tag text.
     *
     * @param string $name New name
     *
     * @return static
     */
    public function setTag(string $name): static
    {
        $this->Tag = $name;
        return $this;
    }

    /**
     * Get associated tag type.
     *
     * @return TagTypeEntityInterface
     */
    public function getTagType(): TagTypeEntityInterface
    {
        return $this->getTableManager()->get('tagtype')->getByPrimaryKey($this->Tag_Type_ID);
    }

    /**
     * Set associated tag type.
     *
     * @param int|TagTypeEntityInterface $type Associated tag type entity or ID, or null
     *
     * @return static
     */
    public function setTagType(int|TagTypeEntityInterface $type): static
    {
        if ($type instanceof TagTypeEntityInterface) {
            $type = $type->getId();
        }
        $this->Tag_Type_ID = $type;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->Tag;
    }
}
