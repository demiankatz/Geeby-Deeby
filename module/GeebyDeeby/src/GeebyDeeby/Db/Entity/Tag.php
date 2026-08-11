<?php

/**
 * Tag entity model.
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
 * Tag entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Tags')]
#[ORM\Index(name: 'tag_type_id', columns: ['Tag_Type_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Tag extends AbstractEntity implements TagEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Tag_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Tag name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Tag', type: 'text', length: 255, nullable: false)]
    protected string $tag;

    /**
     * Tag type.
     *
     * @var TagType
     */
    #[ORM\JoinColumn(
        name: 'Tag_Type_ID',
        referencedColumnName: 'Tag_Type_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: TagType::class)]
    protected TagType $tagType;

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * Get the tag text.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Set the tag text.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setTag(string $name): static
    {
        $this->tag = $name;
        return $this;
    }

    /**
     * Get associated tag type.
     *
     * @return TagTypeEntityInterface
     */
    public function getTagType(): TagTypeEntityInterface
    {
        return $this->tagType;
    }

    /**
     * Set associated tag type.
     *
     * @param int|TagTypeEntityInterface $tagType Associated tagType entity or ID
     *
     * @return static
     */
    public function setTagType(int|TagTypeEntityInterface $tagType): static
    {
        if ($tagType instanceof TagType) {
            $this->tagType = $tagType;
            return $this;
        } elseif ($tagType instanceof TagTypeEntityInterface) {
            $tagType = $tagType->getId();
        }
        $this->tagType = $this->entityManager->getReference(TagType::class, $tagType);
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getTag();
    }
}
