<?php

/**
 * Row Definition for Items_Creators
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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\RoleEntityInterface;

/**
 * Row Definition for Items_Creators
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreators extends TableAwareGateway implements ItemsCreatorEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Item_Creator_ID', 'Items_Creators', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Item_Creator_ID ?? null;
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
     * Get associated person.
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): PersonEntityInterface
    {
        return $this->getTableManager()->get('person')->getByPrimaryKey($this->Person_ID);
    }

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface $person Associated person entity or ID
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface $person): static
    {
        $this->Person_ID = $person instanceof PersonEntityInterface ? $person->getId() : $person;
        return $this;
    }

    /**
     * Get associated role.
     *
     * @return RoleEntityInterface
     */
    public function getRole(): RoleEntityInterface
    {
        return $this->getTableManager()->get('role')->getByPrimaryKey($this->Role_ID);
    }

    /**
     * Set associated role.
     *
     * @param int|RoleEntityInterface $role Associated role entity or ID
     *
     * @return static
     */
    public function setRole(int|RoleEntityInterface $role): static
    {
        $this->Role_ID = $role instanceof RoleEntityInterface ? $role->getId() : $role;
        return $this;
    }
}
