<?php

/**
 * Row Definition for Series
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

use GeebyDeeby\Db\Entity\LanguageEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;

/**
 * Row Definition for Series
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Series extends TableAwareGateway implements SeriesEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Series_ID', 'Series', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Series_ID ?? null;
    }

    /**
     * Get the name of the series.
     *
     * @return string
     */
    public function getSeriesName(): string
    {
        return $this->Series_Name;
    }

    /**
     * Set the name of the series.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setSeriesName(string $name): static
    {
        $this->Series_Name = $name;
        return $this;
    }

    /**
     * Get the description of the series.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->Series_Description;
    }

    /**
     * Set the description of the series.
     *
     * @param ?string $desc New description.
     *
     * @return static
     */
    public function setDescription(?string $desc): static
    {
        $this->Series_Description = $desc;
        return $this;
    }

    /**
     * Get associated language.
     *
     * @return LanguageEntityInterface
     */
    public function getLanguage(): LanguageEntityInterface
    {
        return $this->getTableManager()->get('language')->getByPrimaryKey($this->Language_ID);
    }

    /**
     * Set associated language.
     *
     * @param int|LanguageEntityInterface $language Associated language entity or ID
     *
     * @return static
     */
    public function setLanguage(int|LanguageEntityInterface $language): static
    {
        $this->Language_ID = $language instanceof LanguageEntityInterface ? $language->getId() : $language;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->Series_Name;
    }
}
