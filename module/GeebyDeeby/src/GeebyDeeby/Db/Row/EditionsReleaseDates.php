<?php

/**
 * Row Definition for Editions_Release_Dates
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsReleaseDateEntityInterface;
use GeebyDeeby\Db\Entity\NoteEntityInterface;

/**
 * Row Definition for Editions_Release_Dates
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsReleaseDates extends TableAwareGateway implements EditionsReleaseDateEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Edition_ID', 'Month', 'Day', 'Year'], 'Editions_Release_Dates', $adapter);
    }

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->getTableManager()->get('edition')->getByPrimaryKey($this->Edition_ID);
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static
    {
        $this->Edition_ID = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        return $this;
    }

    /**
     * Get year (or -1 for unpublished).
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->Year;
    }

    /**
     * Set year.
     *
     * @param int $year Year (or -1 for unpublished)
     *
     * @return static
     */
    public function setYear(int $year): static
    {
        $this->Year = $year;
        return $this;
    }

    /**
     * Get month (or 0 for unspecified).
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->Month;
    }

    /**
     * Set month.
     *
     * @param int $month Month (or 0 for unspecified)
     *
     * @return static
     */
    public function setMonth(int $month): static
    {
        $this->Month = $month;
        return $this;
    }

    /**
     * Get day (or 0 for unspecified).
     *
     * @return int
     */
    public function getDay(): int
    {
        return $this->Day;
    }

    /**
     * Set day.
     *
     * @param int $day Day (or 0 for unspecified)
     *
     * @return static
     */
    public function setDay(int $day): static
    {
        $this->Day = $day;
        return $this;
    }

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface
    {
        return $this->Note_ID
            ? $this->getTableManager()->get('note')->getByPrimaryKey($this->Note_ID)
            : null;
    }

    /**
     * Set associated note (if any).
     *
     * @param int|NoteEntityInterface|null $note Associated note entity or ID (null for none)
     *
     * @return static
     */
    public function setNote(int|NoteEntityInterface|null $note): static
    {
        $this->Note_ID = $note instanceof NoteEntityInterface ? $note->getId() : $note;
        return $this;
    }
}
