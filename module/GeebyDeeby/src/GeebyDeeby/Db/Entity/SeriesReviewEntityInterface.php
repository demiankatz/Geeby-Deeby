<?php

/**
 * Interface for series review entity models.
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

use DateTime;

/**
 * Interface for series review entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface SeriesReviewEntityInterface extends EntityInterface
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
     * Get the text of the review.
     *
     * @return string
     */
    public function getReview(): string;

    /**
     * Set the text of the review.
     *
     * @param string $review Review text
     *
     * @return static
     */
    public function setReview(string $review): static;

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

    /**
     * Get the date the review was added.
     *
     * @return DateTime
     */
    public function getAddedDate(): DateTime;

    /**
     * Set the date the review was added.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setAddedDate(string|DateTime $date): static;
}
