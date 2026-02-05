<?php

/**
 * Interface for publisher address entity models.
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

/**
 * Interface for publisher address entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface PublishersAddressEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get associated publisher.
     *
     * @return PublisherEntityInterface
     */
    public function getPublisher(): PublisherEntityInterface;

    /**
     * Set associated publisher.
     *
     * @param int|PublisherEntityInterface $publisher Associated publisher entity or ID
     *
     * @return static
     */
    public function setPublisher(int|PublisherEntityInterface $publisher): static;

    /**
     * Get associated country.
     *
     * @return CountryEntityInterface
     */
    public function getCountry(): CountryEntityInterface;

    /**
     * Set associated country.
     *
     * @param int|CountryEntityInterface $country Associated country entity or ID
     *
     * @return static
     */
    public function setCountry(int|CountryEntityInterface $country): static;

    /**
     * Get associated city.
     *
     * @return ?CityEntityInterface
     */
    public function getCity(): ?CityEntityInterface;

    /**
     * Set associated city.
     *
     * @param int|CityEntityInterface|null $city Associated city entity or ID
     *
     * @return static
     */
    public function setCity(int|CityEntityInterface|null $city): static;

    /**
     * Get the street associated with the address.
     *
     * @return ?string
     */
    public function getStreet(): ?string;

    /**
     * Set the street associated with the address.
     *
     * @param ?string $street New street
     *
     * @return static
     */
    public function setStreet(?string $street): static;
}
