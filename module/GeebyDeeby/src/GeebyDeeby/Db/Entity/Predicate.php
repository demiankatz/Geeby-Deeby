<?php

/**
 * Predicate entity model.
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
 * Predicate entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Predicates')]
#[ORM\Entity]
class Predicate extends AbstractEntity implements PredicateEntityInterface
{
    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Predicate_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Predicate name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Predicate', type: 'string', length: 2048, nullable: false)]
    protected string $predicate;

    /**
     * Predicate abbreviation.
     *
     * @var string
     */
    #[ORM\Column(name: 'Predicate_Abbrev', type: 'string', length: 256, nullable: false)]
    protected string $abbreviation;

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
     * Get the predicate.
     *
     * @return string
     */
    public function getPredicate(): string
    {
        return $this->predicate;
    }

    /**
     * Set the predicate.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setPredicate(string $name): static
    {
        $this->predicate = $name;
        return $this;
    }

    /**
     * Get the abbreviation.
     *
     * @return string
     */
    public function getAbbreviation(): string
    {
        return $this->abbreviation;
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
        $this->abbreviation = $abbrev;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getAbbreviation();
    }
}
