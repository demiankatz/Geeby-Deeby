<?php

/**
 * Row Definition for Countries_URIs
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

use GeebyDeeby\Db\Entity\CountriesUriEntityInterface;
use GeebyDeeby\Db\Entity\CountryEntityInterface;
use GeebyDeeby\Db\Entity\PredicateEntityInterface;

/**
 * Row Definition for Countries_URIs
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CountriesURIs extends TableAwareGateway implements CountriesUriEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'Countries_URIs', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Sequence_ID ?? null;
    }

    /**
     * Get associated country.
     *
     * @return CountryEntityInterface
     */
    public function getCountry(): CountryEntityInterface
    {
        return $this->getTableManager()->get('country')->getByPrimaryKey($this->Country_ID);
    }

    /**
     * Set associated country.
     *
     * @param int|CountryEntityInterface $country Associated country entity or ID
     *
     * @return static
     */
    public function setCountry(int|CountryEntityInterface $country): static
    {
        $this->Country_ID = $country instanceof CountryEntityInterface ? $country->getId() : $country;
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
     * Get the URI associated with the country.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->URI;
    }

    /**
     * Set the URI associated with the country.
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
