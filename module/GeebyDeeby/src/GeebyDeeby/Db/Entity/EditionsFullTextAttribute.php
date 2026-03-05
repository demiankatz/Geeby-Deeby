<?php

/**
 * Edition full text attribute entity model.
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
 * Edition full text attribute entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Full_Text_Attributes')]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsFullTextAttribute extends AbstractEntity implements EditionsFullTextAttributeEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(
        name: 'Editions_Full_Text_Attribute_ID',
        type: 'bigint',
        nullable: false,
        options: ['unsigned' => true]
    )]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Attribute name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Editions_Full_Text_Attribute_Name', type: 'string', length: 255, nullable: false)]
    protected string $attributeName;

    /**
     * RDF property.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Editions_Full_Text_Attribute_RDF_Property', type: 'string', length: 255, nullable: true)]
    protected ?string $rdfProperty;

    /**
     * Is HTML allowed?
     *
     * @var bool
     */
    #[ORM\Column(name: 'Allow_HTML', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $allowHtml = false;

    /**
     * Display priority.
     *
     * @var int
     */
    #[ORM\Column(name: 'Display_Priority', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $displayPriority = 0;

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
     * Get the name of the attribute.
     *
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attributeName;
    }

    /**
     * Set the name of the attribute.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setAttributeName(string $name): static
    {
        $this->attributeName = $name;
        return $this;
    }

    /**
     * Get the RDF property (or null if none).
     *
     * @return ?string
     */
    public function getRdfProperty(): ?string
    {
        return $this->rdfProperty;
    }

    /**
     * Set the RDF property (or null if none).
     *
     * @param ?string $property New RDF property (or null to clear)
     *
     * @return static
     */
    public function setRdfProperty(?string $property): static
    {
        $this->rdfProperty = $property;
        return $this;
    }

    /**
     * Does this attribute allow HTML?
     *
     * @return bool
     */
    public function allowsHtml(): bool
    {
        return $this->allowHtml;
    }

    /**
     * Set whether this attribute allows HTML.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setAllowsHtml(bool $state): static
    {
        $this->allowHtml = $state;
        return $this;
    }

    /**
     * Get the display priority.
     *
     * @return int
     */
    public function getDisplayPriority(): int
    {
        return $this->displayPriority;
    }

    /**
     * Set the display priority.
     *
     * @param int $priority New display priority
     *
     * @return static
     */
    public function setDisplayPriority(int $priority): static
    {
        $this->displayPriority = $priority;
        return $this;
    }
}
