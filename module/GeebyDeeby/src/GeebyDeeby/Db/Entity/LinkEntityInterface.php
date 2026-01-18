<?php

/**
 * Interface for link entity models.
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
 * Interface for link entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface LinkEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the link.
     *
     * @return string
     */
    public function getLinkName(): string;

    /**
     * Set the name of the link.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setLinkName(string $name): static;

    /**
     * Get the URL of the link.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set the URL of the link.
     *
     * @param string $url New url.
     *
     * @return static
     */
    public function setUrl(string $url): static;

    /**
     * Get a description of the link.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Set the link.
     *
     * @param string $description New description
     *
     * @return static
     */
    public function setDescription(string $description): static;

    /**
     * Set the date the link was last checked.
     *
     * @return DateTime
     */
    public function getDateChecked(): DateTime;

    /**
     * Set the date the link was last checked.
     *
     * @param string|DateTime $checked Checked date
     *
     * @return static
     */
    public function setDateChecked(string|DateTime $checked): static;

    /**
     * Get associated link type.
     *
     * @return LinkTypeEntityInterface
     */
    public function getLinkType(): LinkTypeEntityInterface;

    /**
     * Set associated link type.
     *
     * @param int|LinkTypeEntityInterface $linkType Associated language entity or ID
     *
     * @return static
     */
    public function setLinkType(int|LinkTypeEntityInterface $linkType): static;
}
