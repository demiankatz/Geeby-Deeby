<?php

/**
 * Edition attribute value entity model.
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
 * Edition attribute value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Attributes_Values')]
#[ORM\Index(name: 'idx_91a05a58959f1de4', columns: ['Edition_ID'])]
#[ORM\Index(name: 'editions_attribute_id', columns: ['Editions_Attribute_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsAttributesValue extends AbstractEntity implements EditionsAttributesValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Edition.
     *
     * @var Edition
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * Edition attribute.
     *
     * @var EditionsAttribute
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Editions_Attribute_ID',
        referencedColumnName: 'Editions_Attribute_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: EditionsAttribute::class)]
    protected EditionsAttribute $attribute;

    /**
     * Attribute value.
     *
     * @var string
     */
    #[ORM\Column(name: 'Editions_Attribute_Value', type: 'text', length: 16777215, nullable: false)]
    protected string $value;

    /**
     * Get associated edition.
     *
     * @return ?EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->edition;
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface|null $edition): static
    {
        if ($edition instanceof Edition) {
            $this->edition = $edition;
            return $this;
        } elseif ($edition instanceof EditionEntityInterface) {
            $edition = $edition->getId();
        }
        $this->edition = $this->entityManager->getReference(Edition::class, $edition);
        return $this;
    }

    /**
     * Get associated attribute.
     *
     * @return ?EditionsAttributeEntityInterface
     */
    public function getAttribute(): EditionsAttributeEntityInterface
    {
        return $this->attribute;
    }

    /**
     * Set associated attribute.
     *
     * @param int|EditionsAttributeEntityInterface $attribute Associated edition entity or ID
     *
     * @return static
     */
    public function setAttribute(int|EditionsAttributeEntityInterface|null $attribute): static
    {
        if ($attribute instanceof EditionsAttribute) {
            $this->attribute = $attribute;
            return $this;
        } elseif ($attribute instanceof EditionsAttributeEntityInterface) {
            $attribute = $attribute->getId();
        }
        $this->attribute = $this->entityManager->getReference(EditionsAttribute::class, $attribute);
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
