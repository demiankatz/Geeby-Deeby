<?php

/**
 * Row Definition for Tags_Relationships_Values
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
use GeebyDeeby\Db\Entity\TagsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\TagsRelationshipsValueEntityInterface;

/**
 * Row Definition for Tags_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsRelationshipsValues extends TableAwareGateway implements TagsRelationshipsValueEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(
            ['Subject_Tag_ID', 'Tags_Relationship_ID', 'Object_Tag_ID'],
            'Tags_Relationships_Values',
            $adapter
        );
    }

    /**
     * Get subject tag.
     *
     * @return TagEntityInterface
     */
    public function getSubject(): TagEntityInterface
    {
        return $this->getTableManager()->get('tag')->getByPrimaryKey($this->Subject_Tag_ID);
    }

    /**
     * Set subject tag.
     *
     * @param int|TagEntityInterface $tag Subject tag entity or ID
     *
     * @return static
     */
    public function setSubject(int|TagEntityInterface $tag): static
    {
        $this->Subject_Tag_ID = $tag instanceof TagEntityInterface ? $tag->getId() : $tag;
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return TagsRelationshipEntityInterface
     */
    public function getRelationship(): TagsRelationshipEntityInterface
    {
        return $this->getTableManager()->get('tagsrelationship')->getByPrimaryKey($this->Tags_Relationship_ID);
    }

    /**
     * Set relationship.
     *
     * @param int|TagsRelationshipEntityInterface $relationship Relationship entity or ID
     *
     * @return static
     */
    public function setRelationship(int|TagsRelationshipEntityInterface $relationship): static
    {
        $this->Tags_Relationship_ID = $relationship instanceof TagsRelationshipEntityInterface
            ? $relationship->getId() : $relationship;
        return $this;
    }

    /**
     * Get object tag.
     *
     * @return TagEntityInterface
     */
    public function getObject(): TagEntityInterface
    {
        return $this->getTableManager()->get('tag')->getByPrimaryKey($this->Object_Tag_ID);
    }

    /**
     * Set object tag.
     *
     * @param int|TagEntityInterface $tag Object tag entity or ID
     *
     * @return static
     */
    public function setObject(int|TagEntityInterface $tag): static
    {
        $this->Object_Tag_ID = $tag instanceof TagEntityInterface ? $tag->getId() : $tag;
        return $this;
    }
}
