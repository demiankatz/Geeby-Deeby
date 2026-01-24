<?php

/**
 * Row Definition for Users
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

use DateTime;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\Entity\UserGroupEntityInterface;

/**
 * Row Definition for Users
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class User extends TableAwareGateway implements UserEntityInterface
{
    /**
     * Should we disable logging for this class?
     *
     * @var bool
     */
    protected static $doNotLog = true;

    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('User_ID', 'Users', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->User_ID ?? null;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->Username;
    }

    /**
     * Set username.
     *
     * @param string $username Username
     *
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->Username = $username;
        return $this;
    }

    /**
     * Get password hash.
     *
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->Password_Hash;
    }

    /**
     * Set password hash.
     *
     * @param string $hash Password hash
     *
     * @return static
     */
    public function setPasswordHash(string $hash): static
    {
        $this->Password_Hash = $hash;
        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->Name;
    }

    /**
     * Set name.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        $this->Name = $name;
        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->Address;
    }

    /**
     * Set address.
     *
     * @param string $address Address
     *
     * @return static
     */
    public function setAddress(string $address): static
    {
        $this->Address = $address;
        return $this;
    }

    /**
     * Get associated person (if any).
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): ?PersonEntityInterface
    {
        return $this->getTableManager()->get('person')->getByPrimaryKey($this->Person_ID);
    }

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface|null $person Associated person entity or ID (null for none)
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface|null $person): static
    {
        $this->Person_ID = $person instanceof PersonEntityInterface ? $person->getId() : $person;
        return $this;
    }

    /**
     * Get associated user group (if any).
     *
     * @return UserGroupEntityInterface
     */
    public function getUserGroup(): ?UserGroupEntityInterface
    {
        return $this->User_Group_ID
            ? $this->getTableManager()->get('usergroup')->getByPrimaryKey($this->User_Group_ID) : null;
    }

    /**
     * Set associated user group.
     *
     * @param int|UserGroupEntityInterface|null $group Associated user group entity or ID (null for none)
     *
     * @return static
     */
    public function setUserGroup(int|UserGroupEntityInterface|null $group): static
    {
        $this->User_Group_ID = $group instanceof UserGroupEntityInterface ? $group->getId() : $group;
        return $this;
    }

    /**
     * Get join reason.
     *
     * @return string
     */
    public function getJoinReason(): string
    {
        return $this->Join_Reason;
    }

    /**
     * Set join reason.
     *
     * @param string $reason Join reason
     *
     * @return static
     */
    public function setJoinReason(string $reason): static
    {
        $this->Join_Reason = $reason;
        return $this;
    }

    /**
     * Get the date and time the user last logged in.
     *
     * @return DateTime
     */
    public function getLastLoginDate(): DateTime
    {
        return DateTime::createFromFormat('Y-m-d h:i:s', $this->Last_Login);
    }

    /**
     * Set the date and time the user last logged in.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setLastLoginDate(string|DateTime $date): static
    {
        $this->Last_Login = $date instanceof DateTime ? $date->format('Y-m-d h:i:s') : $date;
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
}
