<?php

/**
 * Interface for city entity models.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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
 * Interface for city entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface CitiesUriEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get associated city.
     *
     * @return CityEntityInterface
     */
    public function getCity(): CityEntityInterface;

    /**
     * Set associated city.
     *
     * @param int|CityEntityInterface $city Associated city entity or ID
     *
     * @return static
     */
    public function setCity(int|CityEntityInterface $city): static;

    /**
     * Get associated predicate.
     *
     * @return PredicateEntityInterface
     */
    public function getPredicate(): PredicateEntityInterface;

    /**
     * Set associated predicate.
     *
     * @param int|PredicateEntityInterface $predicate Associated predicate entity or ID
     *
     * @return static
     */
    public function setPredicate(int|PredicateEntityInterface $predicate): static;

    /**
     * Get the URI associated with the city.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Set the URI associated with the city.
     *
     * @param string $uri New URI.
     *
     * @return static
     */
    public function setUri(string $uri): static;
}
