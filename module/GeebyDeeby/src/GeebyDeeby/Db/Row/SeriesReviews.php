<?php

/**
 * Row Definition for Series_Reviews
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use DateTime;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesReviewEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Row Definition for Series_Reviews
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesReviews extends TableAwareGateway implements SeriesReviewEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Series_ID', 'User_ID'], 'Series_Reviews', $adapter);
    }

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->getTableManager()->get('series')->getByPrimaryKey($this->Series_ID);
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static
    {
        $this->Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }

    /**
     * Get associated user.
     *
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface
    {
        return $this->getTableManager()->get('user')->getByPrimaryKey($this->User_ID);
    }

    /**
     * Set associated user.
     *
     * @param int|UserEntityInterface $user Associated user entity or ID
     *
     * @return static
     */
    public function setUser(int|UserEntityInterface $user): static
    {
        $this->User_ID = $user instanceof UserEntityInterface ? $user->getId() : $user;
        return $this;
    }

    /**
     * Get the text of the review.
     *
     * @return string
     */
    public function getReview(): string
    {
        return $this->Review;
    }

    /**
     * Set the text of the review.
     *
     * @param string $review Review text
     *
     * @return static
     */
    public function setReview(string $review): static
    {
        $this->Review = $review;
        return $this;
    }

    /**
     * Is the user approved?
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->Approved === 'y';
    }

    /**
     * Set whether the user is approved.
     *
     * @param bool $approved Is the user approved?
     *
     * @return static
     */
    public function setIsApproved(bool $approved): static
    {
        $this->Approved = $approved ? 'y' : 'n';
        return $this;
    }

    /**
     * Get the date the review was added.
     *
     * @return DateTime
     */
    public function getAddedDate(): DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $this->Added);
    }

    /**
     * Set the date the review was added.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setAddedDate(string|DateTime $date): static
    {
        $this->Added = $date instanceof DateTime ? $date->format('Y-m-d') : $date;
        return $this;
    }
}
