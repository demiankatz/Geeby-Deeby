<?php

/**
 * Publisher address entity model.
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
 * Publisher address entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Publishers_Addresses')]
#[ORM\Index(name: 'publisher_id', columns: ['Publisher_ID'])]
#[ORM\Index(name: 'country_id', columns: ['Country_ID'])]
#[ORM\Index(name: 'city_id', columns: ['City_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class PublishersAddress extends AbstractEntity implements PublishersAddressEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Address_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Publisher.
     *
     * @var Publisher
     */
    #[ORM\JoinColumn(name: 'Publisher_ID', referencedColumnName: 'Publisher_ID', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    protected Publisher $publisher;

    /**
     * Country.
     *
     * @var Country
     */
    #[ORM\JoinColumn(name: 'Country_ID', referencedColumnName: 'Country_ID', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Country::class)]
    protected Country $country;

    /**
     * City.
     *
     * @var City
     */
    #[ORM\JoinColumn(name: 'City_ID', referencedColumnName: 'City_ID', nullable: true)]
    #[ORM\ManyToOne(targetEntity: City::class)]
    protected City $city;

    /**
     * Street.
     *
     * @var string
     */
    #[ORM\Column(name: 'Street', type: 'text', length: 255, nullable: true, options: ['default' => ''])]
    protected string $street = '';

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
     * Get associated publisher.
     *
     * @return PublisherEntityInterface
     */
    public function getPublisher(): PublisherEntityInterface
    {
        return $this->publisher;
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
        if ($publisher instanceof Publisher) {
            $this->publisher = $publisher;
            return $this;
        } elseif ($publisher instanceof PublisherEntityInterface) {
            $publisher = $publisher->getId();
        }
        $this->publisher = $this->entityManager->getReference(Publisher::class, $publisher);
        return $this;
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
     * Get associated city.
     *
     * @return ?CityEntityInterface
     */
    public function getCity(): ?CityEntityInterface
    {
        return $this->city;
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
        if ($city instanceof City || $city === null) {
            $this->city = $city;
            return $this;
        } elseif ($city instanceof CityEntityInterface) {
            $city = $city->getId();
        }
        $this->city = $this->entityManager->getReference(City::class, $city);
        return $this;
    }

    /**
     * Get the street associated with the address.
     *
     * @return ?string
     */
    public function getStreet(): ?string
    {
        return $this->street;
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
        $this->street = $street;
        return $this;
    }
}
