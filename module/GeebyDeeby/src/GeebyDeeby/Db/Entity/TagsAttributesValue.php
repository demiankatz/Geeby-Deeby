<?php

/**
 * Tag attribute value entity model.
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
 * Tag attribute value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Tags_Attributes_Values')]
#[ORM\Index(name: 'idx_1a7a1a191394624d', columns: ['Tag_ID'])]
#[ORM\Index(name: 'tags_attribute_id', columns: ['Tags_Attribute_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class TagsAttributesValue extends AbstractEntity implements TagsAttributesValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Tag.
     *
     * @var Tag
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Tag_ID',
        referencedColumnName: 'Tag_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    protected Tag $tag;

    /**
     * Tag attribute.
     *
     * @var TagsAttribute
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Tags_Attribute_ID',
        referencedColumnName: 'Tags_Attribute_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: TagsAttribute::class)]
    protected TagsAttribute $attribute;

    /**
     * Attribute value.
     *
     * @var string
     */
    #[ORM\Column(name: 'Tags_Attribute_Value', type: 'text', length: 16777215, nullable: false)]
    protected string $value;

    /**
     * Get associated tag.
     *
     * @return ?TagEntityInterface
     */
    public function getTag(): TagEntityInterface
    {
        return $this->tag;
    }

    /**
     * Set associated tag.
     *
     * @param int|TagEntityInterface $tag Associated tag entity or ID
     *
     * @return static
     */
    public function setTag(int|TagEntityInterface|null $tag): static
    {
        if ($tag instanceof Tag) {
            $this->tag = $tag;
            return $this;
        } elseif ($tag instanceof TagEntityInterface) {
            $tag = $tag->getId();
        }
        $this->tag = $this->entityManager->getReference(Tag::class, $tag);
        return $this;
    }

    /**
     * Get associated attribute.
     *
     * @return ?TagsAttributeEntityInterface
     */
    public function getAttribute(): TagsAttributeEntityInterface
    {
        return $this->attribute;
    }

    /**
     * Set associated attribute.
     *
     * @param int|TagsAttributeEntityInterface $attribute Associated tag entity or ID
     *
     * @return static
     */
    public function setAttribute(int|TagsAttributeEntityInterface|null $attribute): static
    {
        if ($attribute instanceof TagsAttribute) {
            $this->attribute = $attribute;
            return $this;
        } elseif ($attribute instanceof TagsAttributeEntityInterface) {
            $attribute = $attribute->getId();
        }
        $this->attribute = $this->entityManager->getReference(TagsAttribute::class, $attribute);
        return $this;
    }

    /**
     * Get the value of the attribute.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
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
        $this->value = $value;
        return $this;
    }
}
