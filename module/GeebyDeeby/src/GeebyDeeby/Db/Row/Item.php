<?php

/**
 * Row Definition for Items
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

use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;

/**
 * Row Definition for Items
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Item extends TableAwareGateway implements ItemEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Item_ID', 'Items', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Item_ID ?? null;
    }

    /**
     * Get the name of the item.
     *
     * @return string
     */
    public function getItemName(): string
    {
        return $this->Item_Name;
    }

    /**
     * Set the name of the item.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setItemName(string $name): static
    {
        $this->Item_Name = $name;
        return $this;
    }

    /**
     * Get errata for the item.
     *
     * @return string
     */
    public function getErrata(): string
    {
        return $this->Item_Errata;
    }

    /**
     * Set errata for the item.
     *
     * @param string $errata New errata
     *
     * @return static
     */
    public function setErrata(string $errata): static
    {
        $this->Item_Errata = $errata;
        return $this;
    }

    /**
     * Get thanks for the item.
     *
     * @return string
     */
    public function getThanks(): string
    {
        return $this->Item_Thanks;
    }

    /**
     * Set thanks for the item.
     *
     * @param string $thanks New thanks
     *
     * @return static
     */
    public function setThanks(string $thanks): static
    {
        $this->Item_Thanks = $thanks;
        return $this;
    }

    /**
     * Get associated material type.
     *
     * @return MaterialTypeEntityInterface
     */
    public function getMaterialType(): MaterialTypeEntityInterface
    {
        return $this->getTableManager()->get('materialtype')->getByPrimaryKey($this->Material_Type_ID);
    }

    /**
     * Set associated material type.
     *
     * @param int|MaterialTypeEntityInterface $materialType Associated language entity or ID
     *
     * @return static
     */
    public function setMaterialType(int|MaterialTypeEntityInterface $materialType): static
    {
        $this->Material_Type_ID = $materialType instanceof MaterialTypeEntityInterface
            ? $materialType->getId() : $materialType;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->Item_Name;
    }
}
