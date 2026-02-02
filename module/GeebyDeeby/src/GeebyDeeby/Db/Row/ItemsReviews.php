<?php

/**
 * Row Definition for Items_Reviews
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
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsReviewEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Row Definition for Items_Reviews
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsReviews extends TableAwareGateway implements ItemsReviewEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Item_ID', 'User_ID'], 'Items_Reviews', $adapter);
    }

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->getTableManager()->get('item')->getByPrimaryKey($this->Item_ID);
    }

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static
    {
        $this->Item_ID = $item instanceof ItemEntityInterface ? $item->getId() : $item;
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
