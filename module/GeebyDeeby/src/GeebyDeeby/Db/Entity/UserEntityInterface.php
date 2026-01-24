<?php

/**
 * Interface for user entity models.
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

use DateTime;

/**
 * Interface for user entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface UserEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Set username.
     *
     * @param string $username Username
     *
     * @return static
     */
    public function setUsername(string $username): static;

    /**
     * Get password hash.
     *
     * @return string
     */
    public function getPasswordHash(): string;

    /**
     * Set password hash.
     *
     * @param string $hash Password hash
     *
     * @return static
     */
    public function setPasswordHash(string $hash): static;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setName(string $name): static;

    /**
     * Get associated person (if any).
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): ?PersonEntityInterface;

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface|null $person Associated person entity or ID (null for none)
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface|null $person): static;

    /**
     * Get associated user group (if any).
     *
     * @return UserGroupEntityInterface
     */
    public function getUserGroup(): ?UserGroupEntityInterface;

    /**
     * Set associated user group.
     *
     * @param int|UserGroupEntityInterface|null $group Associated user group entity or ID (null for none)
     *
     * @return static
     */
    public function setUserGroup(int|UserGroupEntityInterface|null $group): static;

    /**
     * Get join reason.
     *
     * @return string
     */
    public function getJoinReason(): string;

    /**
     * Set join reason.
     *
     * @param string $reason Join reason
     *
     * @return static
     */
    public function setJoinReason(string $reason): static;

    /**
     * Get the date and time the user last logged in.
     *
     * @return DateTime
     */
    public function getLastLoginDate(): DateTime;

    /**
     * Set the date and time the user last logged in.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setLastLoginDate(string|DateTime $date): static;

    /**
     * Is the user approved?
     *
     * @return bool
     */
    public function isApproved(): bool;

    /**
     * Set whether the user is approved.
     *
     * @param bool $approved Is the user approved?
     *
     * @return static
     */
    public function setIsApproved(bool $approved): static;
}
