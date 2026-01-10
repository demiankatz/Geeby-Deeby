<?php

/**
 * Interface for user group entity models.
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
 * Interface for user group entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface UserGroupEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the user group name.
     *
     * @return string
     */
    public function getGroupName(): string;

    /**
     * Set the user group name.
     *
     * @param string $name New user group name
     *
     * @return static
     */
    public function setGroupName(string $name): static;

    /**
     * Does the group grant content editor permission?
     *
     * @return bool
     */
    public function isContentEditor(): bool;

    /**
     * Set whether the group grants content editor permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsContentEditor(bool $state): static;

    /**
     * Does the group grant user editor permission?
     *
     * @return bool
     */
    public function isUserEditor(): bool;

    /**
     * Set whether the group grants user editor permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsUserEditor(bool $state): static;

    /**
     * Does the group grant approver permission?
     *
     * @return bool
     */
    public function isApprover(): bool;

    /**
     * Set whether the group grants approver permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsApprover(bool $state): static;

    /**
     * Does the group grant data manager permission?
     *
     * @return bool
     */
    public function isDataManager(): bool;

    /**
     * Set whether the group grants data manager permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsDataManager(bool $state): static;
}
