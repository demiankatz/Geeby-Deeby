<?php

/**
 * Interface for edition release date entity models.
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
 * Interface for edition release date entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface EditionsReleaseDateEntityInterface extends EntityInterface
{
    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface;

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static;

    /**
     * Get year (or -1 for unpublished).
     *
     * @return int
     */
    public function getYear(): int;

    /**
     * Set year.
     *
     * @param int $year Year (or -1 for unpublished)
     *
     * @return static
     */
    public function setYear(int $year): static;

    /**
     * Get month (or 0 for unspecified).
     *
     * @return int
     */
    public function getMonth(): int;

    /**
     * Set month.
     *
     * @param int $month Month (or 0 for unspecified)
     *
     * @return static
     */
    public function setMonth(int $month): static;

    /**
     * Get day (or 0 for unspecified).
     *
     * @return int
     */
    public function getDay(): int;

    /**
     * Set day.
     *
     * @param int $day Day (or 0 for unspecified)
     *
     * @return static
     */
    public function setDay(int $day): static;

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface;

    /**
     * Set associated note (if any).
     *
     * @param int|NoteEntityInterface|null $note Associated note entity or ID (null for none)
     *
     * @return static
     */
    public function setNote(int|NoteEntityInterface|null $note): static;
}
