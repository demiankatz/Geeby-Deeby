<?php

/**
 * Row Definition for Publishers_Addresses
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

use GeebyDeeby\Db\Entity\CityEntityInterface;
use GeebyDeeby\Db\Entity\CountryEntityInterface;
use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Entity\PublishersAddressEntityInterface;

/**
 * Row Definition for Publishers_Addresses
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersAddresses extends TableAwareGateway implements PublishersAddressEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Address_ID', 'Publishers_Addresses', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Address_ID ?? null;
    }

    /**
     * Get associated publisher.
     *
     * @return PublisherEntityInterface
     */
    public function getPublisher(): PublisherEntityInterface
    {
        return $this->getTableManager()->get('publisher')->getByPrimaryKey($this->Publisher_ID);
    }

    /**
     * Set associated publisher.
     *
     * @param int|PublisherEntityInterface $publisher Associated publisher entity or ID
     *
     * @return static
     */
    public function setPublisher(int|PublisherEntityInterface $publisher): static
    {
        $this->Publisher_ID = $publisher instanceof PublisherEntityInterface ? $publisher->getId() : $publisher;
        return $this;
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
     * Get associated city.
     *
     * @return CityEntityInterface
     */
    public function getCity(): ?CityEntityInterface
    {
        return $this->City_ID
            ? $this->getTableManager()->get('city')->getByPrimaryKey($this->City_ID)
            : null;
    }

    /**
     * Set associated city.
     *
     * @param int|CityEntityInterface|null $city Associated city entity or ID
     *
     * @return static
     */
    public function setCity(int|CityEntityInterface|null $city): static
    {
        $this->City_ID = $city instanceof CityEntityInterface ? $city->getId() : $city;
        return $this;
    }

    /**
     * Get the street associated with the address.
     *
     * @return ?string
     */
    public function getStreet(): ?string
    {
        return $this->Street;
    }

    /**
     * Set the street associated with the address.
     *
     * @param ?string $street New street
     *
     * @return static
     */
    public function setStreet(?string $street): static
    {
        $this->Street = $street;
        return $this;
    }
}
