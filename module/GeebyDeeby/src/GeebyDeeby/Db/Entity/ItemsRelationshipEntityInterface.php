<?php

/**
 * Interface for item relationship entity models.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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

/**
 * Interface for item relationship entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface ItemsRelationshipEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationshipName(): string;

    /**
     * Set the name of the relationship.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setRelationshipName(string $name): static;

    /**
     * Get the RDF property (or null if none).
     *
     * @return ?string
     */
    public function getRdfProperty(): ?string;

    /**
     * Set the RDF property (or null if none).
     *
     * @param ?string $property New RDF property (or null to clear)
     *
     * @return static
     */
    public function setRdfProperty(?string $property): static;

    /**
     * Get the display priority.
     *
     * @return int
     */
    public function getDisplayPriority(): int;

    /**
     * Set the display priority.
     *
     * @param int $priority New display priority
     *
     * @return static
     */
    public function setDisplayPriority(int $priority): static;

    /**
     * Get the name of the inverse version of the relationship (null if none).
     *
     * @return ?string
     */
    public function getInverseRelationshipName(): ?string;

    /**
     * Set the name of the inverse version of the relationship.
     *
     * @param ?string $name New name (null if none).
     *
     * @return static
     */
    public function setInverseRelationshipName(?string $name): static;

    /**
     * Get the RDF property of the inverse version of the relationship (or null if none).
     *
     * @return ?string
     */
    public function getInverseRdfProperty(): ?string;

    /**
     * Set the RDF property of the inverse version of the relationship (or null if none).
     *
     * @param ?string $property New RDF property (or null to clear)
     *
     * @return static
     */
    public function setInverseRdfProperty(?string $property): static;

    /**
     * Get the display priority of the inverse version of the relationship.
     *
     * @return int
     */
    public function getInverseDisplayPriority(): int;

    /**
     * Set the display priority of the inverse version of the relationship.
     *
     * @param int $priority New display priority
     *
     * @return static
     */
    public function setInverseDisplayPriority(int $priority): static;
}
