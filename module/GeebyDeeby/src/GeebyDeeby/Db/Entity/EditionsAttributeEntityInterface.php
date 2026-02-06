<?php

/**
 * Interface for edition attribute entity models.
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
 * Interface for edition attribute entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface EditionsAttributeEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the attribute.
     *
     * @return string
     */
    public function getAttributeName(): string;

    /**
     * Set the name of the attribute.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setAttributeName(string $name): static;

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
     * Does this attribute allow HTML?
     *
     * @return bool
     */
    public function allowsHtml(): bool;

    /**
     * Set whether this attribute allows HTML.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setAllowsHtml(bool $state): static;

    /**
     * Should this attribute be copied to clones?
     *
     * @return bool
     */
    public function copyToClone(): bool;

    /**
     * Set whether this attribute should be copied to clones.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setCopyToClone(bool $state): static;

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
}
