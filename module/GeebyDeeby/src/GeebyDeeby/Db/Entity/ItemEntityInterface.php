<?php

/**
 * Interface for item entity models.
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
 * Interface for item entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface ItemEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the item.
     *
     * @return string
     */
    public function getItemName(): string;

    /**
     * Set the name of the item.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setItemName(string $name): static;

    /**
     * Get errata for the item.
     *
     * @return ?string
     */
    public function getErrata(): ?string;

    /**
     * Set errata for the item.
     *
     * @param ?string $errata New errata
     *
     * @return static
     */
    public function setErrata(?string $errata): static;

    /**
     * Get thanks for the item.
     *
     * @return ?string
     */
    public function getThanks(): ?string;

    /**
     * Set thanks for the item.
     *
     * @param ?string $thanks New thanks
     *
     * @return static
     */
    public function setThanks(?string $thanks): static;

    /**
     * Get associated material type.
     *
     * @return MaterialTypeEntityInterface
     */
    public function getMaterialType(): MaterialTypeEntityInterface;

    /**
     * Set associated material type.
     *
     * @param int|MaterialTypeEntityInterface $materialType Associated language entity or ID
     *
     * @return static
     */
    public function setMaterialType(int|MaterialTypeEntityInterface $materialType): static;

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string;
}
