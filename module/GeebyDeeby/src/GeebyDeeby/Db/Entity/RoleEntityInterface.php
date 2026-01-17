<?php

/**
 * Interface for role entity models.
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
 * Interface for role entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface RoleEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getRoleName(): string;

    /**
     * Set the role name.
     *
     * @param string $name New name
     *
     * @return static
     */
    public function setRoleName(string $name): static;

    /**
     * Get the item creator predicate.
     *
     * @return string
     */
    public function getItemCreatorPredicate(): string;

    /**
     * Set the item creator predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setItemCreatorPredicate(string $predicate): static;

    /**
     * Get the edition credit predicate.
     *
     * @return string
     */
    public function getEditionCreditPredicate(): string;

    /**
     * Set the edition credit predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setEditionCreditPredicate(string $predicate): static;
}
