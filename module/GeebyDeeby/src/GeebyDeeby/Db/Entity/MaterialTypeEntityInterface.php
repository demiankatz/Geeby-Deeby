<?php

/**
 * Interface for material type entity models.
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
 * Interface for material type entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface MaterialTypeEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the singular name of the material type.
     *
     * @return string
     */
    public function getSingularName(): string;

    /**
     * Set the singular name of the material type.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setSingularName(string $name): static;

    /**
     * Get the plural name of the material type.
     *
     * @return string
     */
    public function getPluralName(): string;

    /**
     * Set the plural name of the material type.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setPluralName(string $name): static;

    /**
     * Is this flagged as the default material type?
     *
     * @return bool
     */
    public function isDefault(): bool;

    /**
     * Set the default status of the material type.
     *
     * @param string $state New state
     *
     * @return static
     */
    public function setIsDefault(bool $state): static;

    /**
     * Get the RDF class (or null if none).
     *
     * @return ?string
     */
    public function getRdfClass(): ?string;

    /**
     * Set the RDF class (or null if none).
     *
     * @param ?string $class New RDF class (or null to clear)
     *
     * @return static
     */
    public function setRdfClass(?string $class): static;
}
