<?php

/**
 * Country/URI link entity model.
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
 * Country/URI link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Countries_URIs')]
#[ORM\Index(name: 'country_id', columns: ['Country_ID'])]
#[ORM\Index(name: 'predicate_id', columns: ['Predicate_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class CountriesUri extends AbstractEntity implements CountriesUriEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Sequence_ID', type: 'bigint', options: ['unsigned' => true], nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Country.
     *
     * @var Country
     */
    #[ORM\JoinColumn(name: 'Country_ID', referencedColumnName: 'Country_ID', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Country::class)]
    protected Country $country;

    /**
     * Predicate.
     *
     * @var Predicate
     */
    #[ORM\JoinColumn(name: 'Predicate_ID', referencedColumnName: 'Predicate_ID', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Predicate::class)]
    protected Predicate $predicate;

    /**
     * URI.
     *
     * @var string
     */
    #[ORM\Column(name: 'URI', type: 'string', length: 2048, nullable: false)]
    protected string $uri;

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
     * Get associated country.
     *
     * @return CountryEntityInterface
     */
    public function getCountry(): CountryEntityInterface
    {
        return $this->country;
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
        if ($country instanceof Country) {
            $this->country = $country;
            return $this;
        } elseif ($country instanceof CountryEntityInterface) {
            $country = $country->getId();
        }
        $this->country = $this->entityManager->getReference(Country::class, $country);
        return $this;
    }

    /**
     * Get associated predicate.
     *
     * @return PredicateEntityInterface
     */
    public function getPredicate(): PredicateEntityInterface
    {
        return $this->predicate;
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
        if ($predicate instanceof Predicate) {
            $this->predicate = $predicate;
            return $this;
        } elseif ($predicate instanceof PredicateEntityInterface) {
            $predicate = $predicate->getId();
        }
        $this->predicate = $this->entityManager->getReference(Predicate::class, $predicate);
        return $this;
    }

    /**
     * Get the URI associated with the country.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
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
        $this->uri = $uri;
        return $this;
    }
}
