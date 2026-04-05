<?php

/**
 * Item relationship entity model.
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
 * Item relationship entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Relationships')]
#[ORM\Entity]
class ItemsRelationship extends AbstractEntity implements ItemsRelationshipEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Items_Relationship_ID', type: 'bigint', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Relationship name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Items_Relationship_Name', type: 'string', length: 255, nullable: false)]
    protected string $relationshipName;

    /**
     * RDF property.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Items_Relationship_RDF_Property', type: 'string', length: 255, nullable: true)]
    protected ?string $rdfProperty;

    /**
     * Display priority.
     *
     * @var int
     */
    #[ORM\Column(name: 'Display_Priority', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $displayPriority = 0;

    /**
     * Inverse relationship name.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Items_Inverse_Relationship_Name', type: 'string', length: 255, nullable: true)]
    protected ?string $inverseRelationshipName;

    /**
     * RDF property.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Items_Inverse_Relationship_RDF_Property', type: 'string', length: 255, nullable: true)]
    protected ?string $inverseRdfProperty;

    /**
     * Inverse display priority.
     *
     * @var int
     */
    #[ORM\Column(name: 'Inverse_Display_Priority', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $inverseDisplayPriority = 0;

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
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }

    /**
     * Set the name of the relationship.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setRelationshipName(string $name): static
    {
        $this->relationshipName = $name;
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

    /**
     * Get the name of the inverse version of the relationship (null if none).
     *
     * @return ?string
     */
    public function getInverseRelationshipName(): ?string
    {
        return $this->inverseRelationshipName;
    }

    /**
     * Set the name of the inverse version of the relationship (null if none).
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setInverseRelationshipName(?string $name): static
    {
        $this->inverseRelationshipName = $name;
        return $this;
    }

    /**
     * Get the RDF property of the inverse version of the relationship (or null if none).
     *
     * @return ?string
     */
    public function getInverseRdfProperty(): ?string
    {
        return $this->inverseRdfProperty;
    }

    /**
     * Set the RDF property of the inverse version of the relationship (or null if none).
     *
     * @param ?string $property New RDF property (or null to clear)
     *
     * @return static
     */
    public function setInverseRdfProperty(?string $property): static
    {
        $this->inverseRdfProperty = $property;
        return $this;
    }

    /**
     * Get the display priority of the inverse version of the relationship.
     *
     * @return int
     */
    public function getInverseDisplayPriority(): int
    {
        return $this->inverseDisplayPriority;
    }

    /**
     * Set the display priority of the inverse version of the relationship.
     *
     * @param int $priority New display priority
     *
     * @return static
     */
    public function setInverseDisplayPriority(int $priority): static
    {
        $this->inverseDisplayPriority = $priority;
        return $this;
    }
}
