<?php

/**
 * Row Definition for Predicates
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2015.
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

use GeebyDeeby\Db\Entity\PredicateEntityInterface;

/**
 * Row Definition for Predicates
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Predicate extends RowGateway implements PredicateEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Predicate_ID', 'Predicates', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Predicate_ID ?? null;
    }

    /**
     * Get the predicate.
     *
     * @return string
     */
    public function getPredicate(): string
    {
        return $this->Predicate;
    }

    /**
     * Set the predicate.
     *
     * @param string $predicate New predicate
     *
     * @return static
     */
    public function setPredicate(string $predicate): static
    {
        $this->Predicate = $predicate;
        return $this;
    }

    /**
     * Get the abbreviation.
     *
     * @return string
     */
    public function getAbbreviation(): string
    {
        return $this->Predicate_Abbrev;
    }

    /**
     * Set the abbreviation.
     *
     * @param string $abbrev New abbreviation
     *
     * @return static
     */
    public function setAbbreviation(string $abbrev): static
    {
        $this->Predicate_Abbrev = $abbrev;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->Predicate_Abbrev;
    }
}
