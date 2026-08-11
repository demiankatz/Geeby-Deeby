<?php

/**
 * Role entity model.
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
 * Role entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Roles')]
#[ORM\Entity]
class Role extends AbstractEntity implements RoleEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Role_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Role name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Role_Name', type: 'text', length: 255, nullable: false)]
    protected string $roleName;

    /**
     * Item creator predicate.
     *
     * @var ?string
     */
    #[ORM\Column(
        name: 'Item_Creator_Predicate',
        type: 'string',
        length: 2048,
        nullable: true,
        options: ['default' => null]
    )]
    protected ?string $itemCreatorPredicate = null;

    /**
     * Edition credit predicate.
     *
     * @var ?string
     */
    #[ORM\Column(
        name: 'Edition_Credit_Predicate',
        type: 'string',
        length: 2048,
        nullable: true,
        options: ['default' => null]
    )]
    protected ?string $editionCreditPredicate = null;

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
     * Get the role name.
     *
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

    /**
     * Set the role name.
     *
     * @param string $role New role name
     *
     * @return static
     */
    public function setRoleName(string $role): static
    {
        $this->roleName = $role;
        return $this;
    }

    /**
     * Get the item creator predicate.
     *
     * @return string
     */
    public function getItemCreatorPredicate(): string
    {
        return $this->itemCreatorPredicate;
    }

    /**
     * Set the item creator predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setItemCreatorPredicate(string $predicate): static
    {
        $this->itemCreatorPredicate = $predicate;
        return $this;
    }

    /**
     * Get the edition credit predicate.
     *
     * @return string
     */
    public function getEditionCreditPredicate(): string
    {
        return $this->editionCreditPredicate;
    }

    /**
     * Set the edition credit predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setEditionCreditPredicate(string $predicate): static
    {
        $this->editionCreditPredicate = $predicate;
        return $this;
    }
}
