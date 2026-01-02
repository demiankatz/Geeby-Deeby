<?php

/**
 * Row Definition for People
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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

use GeebyDeeby\Db\Entity\AuthorityEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;

/**
 * Row Definition for People
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Person extends TableAwareGateway implements PersonEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Person_ID', 'People', $adapter);
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName()
    {
        $n = $this->First_Name . ' ' . $this->Last_Name
            . ' ' . $this->Extra_Details;
        return trim(preg_replace(['/\s+/', '/\s+,/'], [' ', ','], $n));
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->First_Name;
    }

    /**
     * Set first name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setFirstName(string $name): static
    {
        $this->First_Name = $name;
        return $this;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->Last_Name;
    }

    /**
     * Set last name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setLastName(string $name): static
    {
        $this->Last_Name = $name;
        return $this;
    }

    /**
     * Get extra details.
     *
     * @return string
     */
    public function getExtraDetails(): string
    {
        return $this->Extra_Details;
    }

    /**
     * Set extra details.
     *
     * @param string $details New value
     *
     * @return static
     */
    public function setExtraDetails(string $details): static
    {
        $this->Extra_Details = $details;
        return $this;
    }

    /**
     * Get biography.
     *
     * @return string
     */
    public function getBiography(): string
    {
        return $this->Biography;
    }

    /**
     * Set biography.
     *
     * @param string $bio New value
     *
     * @return static
     */
    public function setBiography(string $bio): static
    {
        $this->Biography = $bio;
        return $this;
    }

    /**
     * Get associated authority (if any).
     *
     * @return ?AuthorityEntityInterface
     */
    public function getAuthority(): ?AuthorityEntityInterface
    {
        return $this->getTableManager()->get('authority')->getByPrimaryKey($this->Authority_ID);
    }

    /**
     * Set associated authority.
     *
     * @param null|int|AuthorityEntityInterface $authority Associated authority entity or ID, or null
     *
     * @return static
     */
    public function setAuthority(null|int|AuthorityEntityInterface $authority): static
    {
        if ($authority instanceof AuthorityEntityInterface) {
            $authority = $authority->Authority_ID;
        }
        $this->Authority_ID = $authority;
        return $this;
    }
}
