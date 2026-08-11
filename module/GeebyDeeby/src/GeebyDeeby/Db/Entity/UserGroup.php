<?php

/**
 * User group entity model.
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

use Doctrine\ORM\Mapping as ORM;

/**
 * User group entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'User_Groups')]
#[ORM\Entity]
class UserGroup extends AbstractEntity implements UserGroupEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'User_Group_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Tag type name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Group_Name', type: 'text', length: 255, nullable: false)]
    protected string $groupName;

    /**
     * Does this group provide content editor permissions?
     */
    #[ORM\Column(name: 'Content_Editor', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $contentEditor = false;

    /**
     * Does this group provide user editor permissions?
     */
    #[ORM\Column(name: 'User_Editor', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $userEditor = false;

    /**
     * Does this group provide approver permissions?
     */
    #[ORM\Column(name: 'Approver', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $approver = false;

    /**
     * Does this group provide data manager permissions?
     */
    #[ORM\Column(name: 'Data_Manager', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $dataManager = false;

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * Get the user group name.
     *
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
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
        $this->groupName = $name;
        return $this;
    }

    /**
     * Does the group grant content editor permission?
     *
     * @return bool
     */
    public function isContentEditor(): bool
    {
        return $this->contentEditor;
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
        $this->contentEditor = $state;
        return $this;
    }

    /**
     * Does the group grant user editor permission?
     *
     * @return bool
     */
    public function isUserEditor(): bool
    {
        return $this->userEditor;
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
        $this->userEditor = $state;
        return $this;
    }

    /**
     * Does the group grant approver permission?
     *
     * @return bool
     */
    public function isApprover(): bool
    {
        return $this->approver;
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
        $this->approver = $state;
        return $this;
    }

    /**
     * Does the group grant data manager permission?
     *
     * @return bool
     */
    public function isDataManager(): bool
    {
        return $this->dataManager;
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
        $this->dataManager = $state;
        return $this;
    }
}
