<?php

/**
 * Row Definition for Roles
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

use GeebyDeeby\Db\Entity\RoleEntityInterface;

/**
 * Row Definition for Roles
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Role extends RowGateway implements RoleEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Role_ID', 'Roles', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Role_ID ?? null;
    }

    /**
     * Get the role name.
     *
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->Role_Name;
    }

    /**
     * Set the role name.
     *
     * @param string $name New name
     *
     * @return static
     */
    public function setRoleName(string $name): static
    {
        $this->Role_Name = $name;
        return $this;
    }

    /**
     * Get the item creator predicate.
     *
     * @return string
     */
    public function getItemCreatorPredicate(): string
    {
        return $this->Item_Creator_Predicate;
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
        $this->Item_Creator_Predicate = $predicate;
        return $this;
    }

    /**
     * Get the edition credit predicate.
     *
     * @return string
     */
    public function getEditionCreditPredicate(): string
    {
        return $this->Edition_Credit_Predicate;
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
        $this->Edition_Credit_Predicate = $predicate;
        return $this;
    }
}
