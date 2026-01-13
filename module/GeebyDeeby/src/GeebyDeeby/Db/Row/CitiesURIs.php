<?php

/**
 * Row Definition for Cities_URIs
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

use GeebyDeeby\Db\Entity\CitiesUriEntityInterface;
use GeebyDeeby\Db\Entity\CityEntityInterface;
use GeebyDeeby\Db\Entity\PredicateEntityInterface;

/**
 * Row Definition for Cities_URIs
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CitiesURIs extends TableAwareGateway implements CitiesUriEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'Cities_URIs', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Sequence_ID;
    }

    /**
     * Get associated city.
     *
     * @return CityEntityInterface
     */
    public function getCity(): CityEntityInterface
    {
        return $this->getTableManager()->get('city')->getByPrimaryKey($this->City_ID);
    }

    /**
     * Set associated city.
     *
     * @param int|CityEntityInterface $city Associated city entity or ID
     *
     * @return static
     */
    public function setCity(int|CityEntityInterface $city): static
    {
        $this->City_ID = $city instanceof CityEntityInterface ? $city->getId() : $city;
        return $this;
    }

    /**
     * Get associated predicate.
     *
     * @return PredicateEntityInterface
     */
    public function getPredicate(): PredicateEntityInterface
    {
        return $this->getTableManager()->get('predicate')->getByPrimaryKey($this->Predicate_ID);
    }

    /**
     * Set associated predicate.
     *
     * @param int|PredicateEntityInterface $predicate Associated predicate entity or ID
     *
     * @return static
     */
    public function setPredicate(int|PredicateEntityInterface $predicate): static
    {
        $this->Predicate_ID = $predicate instanceof PredicateEntityInterface ? $predicate->getId() : $predicate;
        return $this;
    }

    /**
     * Get the URI associated with the city.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->URI;
    }

    /**
     * Set the URI associated with the city.
     *
     * @param string $uri New URI.
     *
     * @return static
     */
    public function setUri(string $uri): static
    {
        $this->URI = $uri;
        return $this;
    }
}
