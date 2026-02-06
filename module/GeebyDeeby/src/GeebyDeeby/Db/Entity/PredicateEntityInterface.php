<?php

/**
 * Interface for predicate entity models.
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
 * Interface for predicate entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface PredicateEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the predicate.
     *
     * @return string
     */
    public function getPredicate(): string;

    /**
     * Set the predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setPredicate(string $predicate): static;

    /**
     * Get the abbreviation.
     *
     * @return string
     */
    public function getAbbreviation(): string;

    /**
     * Set the abbreviation.
     *
     * @param string $abbrev New abbreviation
     *
     * @return static
     */
    public function setAbbreviation(string $abbrev): static;

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string;
}
