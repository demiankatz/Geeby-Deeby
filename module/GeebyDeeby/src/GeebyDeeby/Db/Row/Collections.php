<?php

/**
 * Row Definition for Collections
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

use GeebyDeeby\Db\Entity\CollectionEntityInterface;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Row Definition for Collections
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CitiesURIs extends TableAwareGateway implements CollectionEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Series_ID', 'Item_ID', 'User_ID', 'Collection_Status'], 'Cities_URIs', $adapter);
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
     * Get collection status (have, want, or extra).
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->Collection_Status;
    }

    /**
     * Set collection status (have, want, or extra).
     *
     * @param string $status New status
     *
     * @return static
     */
    public function setStatus(string $status): static
    {
        $this->Collection_Status = $status;
        return $this;
    }

    /**
     * Get the note associated with the collection entry.
     *
     * @return ?string
     */
    public function getNote(): ?string
    {
        return $this->Collection_Note;
    }

    /**
     * Set the note associated with the collection entry.
     *
     * @param ?string $note New note (null to clear)
     *
     * @return static
     */
    public function setNote(?string $note): static
    {
        $this->Collection_Note = $note;
        return $this;
    }
}
