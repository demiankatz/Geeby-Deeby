<?php

/**
 * Row Definition for Series_Attributes
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

use GeebyDeeby\Db\Entity\SeriesAttributeEntityInterface;

/**
 * Row Definition for Series_Attributes
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesAttribute extends RowGateway implements SeriesAttributeEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Series_Attribute_ID', 'Series_Attributes', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Series_Attribute_ID;
    }

    /**
     * Get the name of the attribute.
     *
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->Series_Attribute_Name;
    }

    /**
     * Set the name of the attribute.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setAttributeName(string $name): static
    {
        $this->Series_Attribute_Name = $name;
        return $this;
    }

    /**
     * Get the RDF property (or null if none).
     *
     * @return ?string
     */
    public function getRdfProperty(): ?string
    {
        return $this->Series_Attribute_RDF_Property;
    }

    /**
     * Set the RDF property (or null if none).
     *
     * @param ?string $property New RDF property (or null to clear)
     *
     * @return static
     */
    public function setRdfProperty(?string $property): static
    {
        $this->Series_Attribute_RDF_Property = $property;
        return $this;
    }

    /**
     * Does this attribute allow HTML?
     *
     * @return bool
     */
    public function allowsHtml(): bool
    {
        return (bool)$this->Allow_HTML;
    }

    /**
     * Set whether this attribute allows HTML.
     *
     * @param bool $state New state
     *
     * @return static
     */
    public function setAllowsHtml(bool $state): static
    {
        $this->Allow_HTML = $state ? 1 : 0;
        return $this;
    }

    /**
     * Get the display priority.
     *
     * @return int
     */
    public function getDisplayPriority(): int
    {
        return $this->Display_Priority;
    }

    /**
     * Set the display priority.
     *
     * @param int $priority New display priority
     *
     * @return static
     */
    public function setDisplayPriority(int $priority): static
    {
        $this->Display_Priority = $priority;
        return $this;
    }

    /**
     * Get the value link (null if none).
     *
     * @return ?string
     */
    public function getValueLink(): ?string
    {
        return $this->Value_Link;
    }

    /**
     * Set the value link.
     *
     * @param ?string $link New value link (null for none)
     *
     * @return static
     */
    public function setValueLink(?string $link): static
    {
        $this->Value_Link = $link;
        return $this;
    }
}
