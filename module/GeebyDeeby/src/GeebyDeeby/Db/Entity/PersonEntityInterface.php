<?php

/**
 * Interface for person entity models.
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
 * Interface for person entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface PersonEntityInterface extends EntityInterface
{
    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Set first name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setFirstName(string $name): static;

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Set last name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setLastName(string $name): static;

    /**
     * Get extra details.
     *
     * @return string
     */
    public function getExtraDetails(): string;

    /**
     * Set extra details.
     *
     * @param string $details New value
     *
     * @return static
     */
    public function setExtraDetails(string $details): static;

    /**
     * Get biography.
     *
     * @return string
     */
    public function getBiography(): string;

    /**
     * Set biography.
     *
     * @param string $bio New value
     *
     * @return static
     */
    public function setBiography(string $bio): static;

    /**
     * Get associated authority (if any).
     *
     * @return ?AuthorityEntityInterface
     */
    public function getAuthority(): ?AuthorityEntityInterface;

    /**
     * Set associated authority.
     *
     * @param null|int|AuthorityEntityInterface $authority Associated authority entity or ID, or null
     *
     * @return static
     */
    public function setAuthority(null|int|AuthorityEntityInterface $authority): static;
}
