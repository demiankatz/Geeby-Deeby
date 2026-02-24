<?php

/**
 * Database service for the Publishers_Addresses table.
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
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\City;
use GeebyDeeby\Db\Entity\Country;
use GeebyDeeby\Db\Entity\PublishersAddress;
use GeebyDeeby\Db\Entity\PublishersAddressEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Publishers_Addresses table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersAddressService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     */
    #[Autowire()]
    public function __construct(
        protected EntityManager $entityManager,
        PersistenceManager $persistenceManager,
    ) {
        parent::__construct($persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return PublishersAddressEntityInterface
     */
    public function createEntity(): PublishersAddressEntityInterface
    {
        $entity = new PublishersAddress();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of addresses for the specified publisher.
     *
     * @param int $pubID Publisher ID
     *
     * @return array
     */
    public function getAddressesForPublisher(int $pubID): array
    {
        $dql = 'SELECT pa.id AS Address_ID, pa.street AS Street, '
            . 'ci.id as City_ID, ci.cityName as City_Name, '
            . 'co.id as Country_ID, co.countryName as Country_Name '
            . 'FROM ' . PublishersAddress::class . ' pa INNER JOIN ' . Country::class . ' co ON pa.country = co.id '
            . 'LEFT JOIN ' . City::class . ' ci ON pa.city = ci.id '
            . 'WHERE pa.publisher = :publisher ORDER BY co.countryName, ci.cityName, pa.street';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('publisher', $pubID);
        return $query->getResult();
    }

    /**
     * Retrieve a specific address for a specific publisher.
     *
     * @param int $publisherId Publisher ID
     * @param int $addressId   Address ID
     *
     * @return ?
     */
    public function getAddressesForPublisherPublisherAddress(
        int $publisherId,
        int $addressId
    ): ?PublishersAddressEntityInterface {
        $dql = 'SELECT p FROM ' . PublishersAddress::class . ' p WHERE p.publisher = :publisher AND p.id = :address';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['publisher' => $publisherId, 'address' => $addressId]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
