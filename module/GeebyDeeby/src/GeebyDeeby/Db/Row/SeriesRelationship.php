<?php

/**
 * Row Definition for Series_Relationships
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2019.
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

use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;

/**
 * Row Definition for Series_Relationships
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesRelationship extends RowGateway implements SeriesRelationshipEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(
            'Series_Relationship_ID',
            'Series_Relationships',
            $adapter
        );
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Series_Relationship_ID;
    }

    /**
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationshipName(): string
    {
        return $this->Series_Relationship_Name;
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
        $this->Series_Relationship_Name = $name;
        return $this;
    }

    /**
     * Get the RDF property (or null if none).
     *
     * @return ?string
     */
    public function getRdfProperty(): ?string
    {
        return $this->Series_Relationship_RDF_Property;
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
        $this->Series_Relationship_RDF_Property = $property;
        return $this;
    }

    /**
     * Get the display priority.
     *
     * @return int
     */
    public function getDisplayPriority(): int
    {
        return $this->Display_Priority;
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
        $this->Display_Priority = $priority;
        return $this;
    }

    /**
     * Get the name of the inverse version of the relationship (null if none).
     *
     * @return ?string
     */
    public function getInverseRelationshipName(): ?string
    {
        return $this->Series_Inverse_Relationship_Name;
    }

    /**
     * Set the name of the inverse version of the relationship.
     *
     * @param ?string $name New name (null if none).
     *
     * @return static
     */
    public function setInverseRelationshipName(?string $name): static
    {
        $this->Series_Inverse_Relationship_Name = $name;
        return $this;
    }

    /**
     * Get the RDF property of the inverse version of the relationship (or null if none).
     *
     * @return ?string
     */
    public function getInverseRdfProperty(): ?string
    {
        return $this->Series_Inverse_Relationship_RDF_Property;
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
        $this->Series_Inverse_Relationship_RDF_Property = $property;
        return $this;
    }

    /**
     * Get the display priority of the inverse version of the relationship.
     *
     * @return int
     */
    public function getInverseDisplayPriority(): int
    {
        return $this->Inverse_Display_Priority;
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
        $this->Inverse_Display_Priority = $priority;
        return $this;
    }
}
