<?php

/**
 * Tag relationship value entity model.
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
 * Tag relationship value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Tags_Relationships_Values')]
#[ORM\Index(name: 'idx_b366ba7e56e9d3b2', columns: ['Subject_Tag_ID'])]
#[ORM\Index(name: 'tags_relationship_id', columns: ['Tags_Relationship_ID'])]
#[ORM\Index(name: 'object_tag_id', columns: ['Object_Tag_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class TagsRelationshipsValue extends AbstractEntity implements TagsRelationshipsValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Subject tag.
     *
     * @var Tag
     */
    #[ORM\JoinColumn(
        name: 'Subject_Tag_ID',
        referencedColumnName: 'Tag_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    protected Tag $subjectTag;

    /**
     * Relationship.
     *
     * @var Tag
     */
    #[ORM\JoinColumn(
        name: 'Tags_Relationship_ID',
        referencedColumnName: 'Tags_Relationship_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TagsRelationship::class)]
    protected TagsRelationship $relationship;

    /**
     * Object tag.
     *
     * @var Tag
     */
    #[ORM\JoinColumn(
        name: 'Object_Tag_ID',
        referencedColumnName: 'Tag_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    protected Tag $objectTag;

    /**
     * Get subject tag.
     *
     * @return TagEntityInterface
     */
    public function getSubject(): TagEntityInterface
    {
        return $this->subjectTag;
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
        if ($tag instanceof Tag) {
            $this->subjectTag = $tag;
            return $this;
        } elseif ($tag instanceof TagEntityInterface) {
            $tag = $tag->getId();
        }
        $this->subjectTag = $this->entityManager->getReference(Tag::class, $tag);
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return TagsRelationshipEntityInterface
     */
    public function getRelationship(): TagsRelationshipEntityInterface
    {
        return $this->relationship;
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
        if ($relationship instanceof TagsRelationship) {
            $this->relationship = $relationship;
            return $this;
        } elseif ($relationship instanceof TagsRelationshipEntityInterface) {
            $relationship = $relationship->getId();
        }
        $this->relationship = $this->entityManager->getReference(TagsRelationship::class, $relationship);
        return $this;
    }

    /**
     * Get object tag.
     *
     * @return TagEntityInterface
     */
    public function getObject(): TagEntityInterface
    {
        return $this->objectTag;
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
        if ($tag instanceof Tag) {
            $this->objectTag = $tag;
            return $this;
        } elseif ($tag instanceof TagEntityInterface) {
            $tag = $tag->getId();
        }
        $this->objectTag = $this->entityManager->getReference(Tag::class, $tag);
        return $this;
    }
}
