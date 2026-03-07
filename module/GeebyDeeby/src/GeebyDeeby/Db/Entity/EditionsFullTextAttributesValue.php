<?php

/**
 * Edition full text attribute value entity model.
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
 * Edition full text attribute value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Full_Text_Attributes_Values')]
#[ORM\Index(name: 'idx_8d2ad899a4760cd3', columns: ['Editions_Full_Text_ID'])]
#[ORM\Index(name: 'editions_full_text_attribute_id', columns: ['Editions_Full_Text_Attribute_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsFullTextAttributesValue extends AbstractEntity implements EditionsFullTextAttributesValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Full text.
     *
     * @var EditionsFullText
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Editions_Full_Text_ID',
        referencedColumnName: 'Sequence_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: EditionsFullText::class)]
    protected EditionsFullText $fullText;

    /**
     * Edition full text attribute.
     *
     * @var EditionsFullTextAttribute
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Editions_Full_Text_Attribute_ID',
        referencedColumnName: 'Editions_Full_Text_Attribute_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: EditionsFullTextAttribute::class)]
    protected EditionsFullTextAttribute $attribute;

    /**
     * Attribute value.
     *
     * @var string
     */
    #[ORM\Column(name: 'Editions_Full_Text_Attribute_Value', type: 'text', length: 16777215, nullable: false)]
    protected string $value;

    /**
     * Get associated edition full text.
     *
     * @return ?EditionsFullTextEntityInterface
     */
    public function getEditionFullText(): EditionsFullTextEntityInterface
    {
        return $this->fullText;
    }

    /**
     * Set associated edition full text.
     *
     * @param int|EditionsFullTextEntityInterface $fulltext Associated edition entity or ID
     *
     * @return static
     */
    public function setEditionFullText(int|EditionsFullTextEntityInterface|null $fulltext): static
    {
        if ($fulltext instanceof EditionsFullText) {
            $this->fullText = $fulltext;
            return $this;
        } elseif ($fulltext instanceof EditionsFullTextEntityInterface) {
            $fulltext = $fulltext->getId();
        }
        $this->fullText = $this->entityManager->getReference(EditionsFullText::class, $fulltext);
        return $this;
    }

    /**
     * Get associated attribute.
     *
     * @return ?EditionsFullTextAttributeEntityInterface
     */
    public function getAttribute(): EditionsFullTextAttributeEntityInterface
    {
        return $this->attribute;
    }

    /**
     * Set associated attribute.
     *
     * @param int|EditionsFullTextAttributeEntityInterface $attribute Associated edition entity or ID
     *
     * @return static
     */
    public function setAttribute(int|EditionsFullTextAttributeEntityInterface|null $attribute): static
    {
        if ($attribute instanceof EditionsFullTextAttribute) {
            $this->attribute = $attribute;
            return $this;
        } elseif ($attribute instanceof EditionsFullTextAttributeEntityInterface) {
            $attribute = $attribute->getId();
        }
        $this->attribute = $this->entityManager->getReference(EditionsFullTextAttribute::class, $attribute);
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
