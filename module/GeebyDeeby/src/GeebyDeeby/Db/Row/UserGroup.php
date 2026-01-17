<?php

/**
 * Row Definition for User_Groups
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

use GeebyDeeby\Db\Entity\UserGroupEntityInterface;

/**
 * Row Definition for User_Groups
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class UserGroup extends RowGateway implements UserGroupEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('User_Group_ID', 'User_Groups', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->User_Group_ID ?? null;
    }

    /**
     * Get the user group name.
     *
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->Group_Name;
    }

    /**
     * Set the user group name.
     *
     * @param string $name New user group name
     *
     * @return static
     */
    public function setGroupName(string $name): static
    {
        $this->Group_Name = $name;
        return $this;
    }

    /**
     * Does the group grant content editor permission?
     *
     * @return bool
     */
    public function isContentEditor(): bool
    {
        return (bool)$this->Content_Editor;
    }

    /**
     * Set whether the group grants content editor permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsContentEditor(bool $state): static
    {
        $this->Content_Editor = $state ? 1 : 0;
        return $this;
    }

    /**
     * Does the group grant user editor permission?
     *
     * @return bool
     */
    public function isUserEditor(): bool
    {
        return (bool)$this->User_Editor;
    }

    /**
     * Set whether the group grants user editor permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsUserEditor(bool $state): static
    {
        $this->User_Editor = $state ? 1 : 0;
        return $this;
    }

    /**
     * Does the group grant approver permission?
     *
     * @return bool
     */
    public function isApprover(): bool
    {
        return $this->Approver;
    }

    /**
     * Set whether the group grants approver permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsApprover(bool $state): static
    {
        $this->Approver = $state ? 1 : 0;
        return $this;
    }

    /**
     * Does the group grant data manager permission?
     *
     * @return bool
     */
    public function isDataManager(): bool
    {
        return $this->Data_Manager;
    }

    /**
     * Set whether the group grants data manager permission.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setIsDataManager(bool $state): static
    {
        $this->Data_Manager = $state ? 1 : 0;
        return $this;
    }
}
