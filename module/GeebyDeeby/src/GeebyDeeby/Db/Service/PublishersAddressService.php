<?php

/**
 * Database service for the Publishers_Addresses table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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

use GeebyDeeby\Db\Entity\PublishersAddressEntityInterface;
use GeebyDeeby\Db\Table\PublishersAddresses;
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
     * @param PublishersAddresses $publishersAddressesTable PublishersAddresses table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PublishersAddresses $publishersAddressesTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PublishersAddressEntityInterface
     */
    public function createEntity(): PublishersAddressEntityInterface
    {
        return $this->publishersAddressesTable->createRow();
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
        return iterator_to_array($this->publishersAddressesTable->getAddressesForPublisher($pubID));
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
        $result = $this->publishersAddressesTable
            ->select(['Publisher_ID' => $publisherId, 'Address_ID' => $addressId]);
        foreach ($result as $current) {
            return $current;
        }
        return null;
    }
}
