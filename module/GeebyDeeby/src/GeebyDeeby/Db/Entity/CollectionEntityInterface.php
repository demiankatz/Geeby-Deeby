<?php

/**
 * Interface for collection entity models.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
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
 * Interface for collection entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface CollectionEntityInterface extends EntityInterface
{
    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface;

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static;

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface;

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static;

    /**
     * Get associated user.
     *
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface;

    /**
     * Set associated user.
     *
     * @param int|UserEntityInterface $user Associated user entity or ID
     *
     * @return static
     */
    public function setUser(int|UserEntityInterface $user): static;

    /**
     * Get collection status (have, want, or extra).
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Set collection status (have, want, or extra).
     *
     * @param string $status New status
     *
     * @return static
     */
    public function setStatus(string $status): static;

    /**
     * Get the note associated with the collection entry.
     *
     * @return ?string
     */
    public function getNote(): ?string;

    /**
     * Set the note associated with the collection entry.
     *
     * @param ?string $note New note (null to clear)
     *
     * @return static
     */
    public function setNote(?string $note): static;
}
