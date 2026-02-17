<?php

/**
 * Material type entity model.
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
 * Material type entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Material_Types')]
#[ORM\Entity]
class MaterialType extends AbstractEntity implements MaterialTypeEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Material_Type_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Singular name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Material_Type_Name', type: 'text', length: 255, nullable: false)]
    protected string $singularName;

    /**
     * Plural name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Material_Type_Plural_Name', type: 'text', length: 255, nullable: false)]
    protected string $pluralName;

    /**
     * RDF class.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Material_Type_RDF_Class', type: 'text', length: 255, nullable: true)]
    protected ?string $rdfClass;

    /**
     * Is this the default value?
     *
     * @var bool
     */
    #[ORM\Column(name: 'Is_Default', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $default = false;

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the singular name of the material type.
     *
     * @return string
     */
    public function getSingularName(): string
    {
        return $this->singularName;
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
        $this->singularName = $name;
        return $this;
    }

    /**
     * Get the plural name of the material type.
     *
     * @return string
     */
    public function getPluralName(): string
    {
        return $this->pluralName;
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
        $this->pluralName = $name;
        return $this;
    }

    /**
     * Is this flagged as the default material type?
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
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
        $this->default = $state;
        return $this;
    }

    /**
     * Get the RDF class (or null if none).
     *
     * @return ?string
     */
    public function getRdfClass(): ?string
    {
        return $this->rdfClass;
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
        $this->rdfClass = $class;
        return $this;
    }
}
