<?php

/**
 * Row Definition for Material Types
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

use GeebyDeeby\Db\Entity\MaterialTypeEntityInterface;

/**
 * Row Definition for Material Types
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class MaterialType extends RowGateway implements MaterialTypeEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Material_Type_ID', 'Material_Types', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Material_Type_ID ?? null;
    }

    /**
     * Get the singular name of the material type.
     *
     * @return string
     */
    public function getSingularName(): string
    {
        return $this->Material_Type_Name;
    }

    /**
     * Set the singular name of the material type.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setSingularName(string $name): static
    {
        $this->Material_Type_Name = $name;
        return $this;
    }

    /**
     * Get the plural name of the material type.
     *
     * @return string
     */
    public function getPluralName(): string
    {
        return $this->Material_Type_Plural_Name;
    }

    /**
     * Set the plural name of the material type.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setPluralName(string $name): static
    {
        $this->Material_Type_Plural_Name = $name;
        return $this;
    }

    /**
     * Is this flagged as the default material type?
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return (bool)$this->Is_Default;
    }

    /**
     * Set the default status of the material type.
     *
     * @param string $state New state
     *
     * @return static
     */
    public function setIsDefault(bool $state): static
    {
        $this->Is_Default = $state ? 1 : 0;
        return $this;
    }

    /**
     * Get the RDF class (or null if none).
     *
     * @return ?string
     */
    public function getRdfClass(): ?string
    {
        return $this->Material_Type_RDF_Class;
    }

    /**
     * Set the RDF class (or null if none).
     *
     * @param ?string $class New RDF class (or null to clear)
     *
     * @return static
     */
    public function setRdfClass(?string $class): static
    {
        $this->Material_Type_RDF_Class = $class;
        return $this;
    }
}
