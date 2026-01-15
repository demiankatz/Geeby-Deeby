<?php

/**
 * Database service for the Publishers_URIs table.
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

use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Entity\PublishersUriEntityInterface;
use GeebyDeeby\Db\Table\PublishersURIs;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Publishers_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersUriService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param PublishersURIs $publishersUrisTable PublishersURIs table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected PublishersURIs $publishersUrisTable
    ) {
    }

    /**
     * Create an empty entity
     *
     * @return PublishersUriEntityInterface
     */
    public function createEntity(): PublishersUriEntityInterface
    {
        return $this->publishersUrisTable->createRow();
    }

    /**
     * Get a list of publishers for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getPublishersForURI(string $uri): array
    {
        return iterator_to_array($this->publishersUrisTable->getPublishersForURI($uri));
    }

    /**
     * Get a list of URIs for the specified publisher.
     *
     * @param int|PublisherEntityInterface $publisher Publisher entity or ID
     *
     * @return array
     */
    public function getURIsForPublisher(int|PublisherEntityInterface $publisher): array
    {
        $publisherId = $publisher instanceof PublisherEntityInterface ? $publisher->getId() : $publisher;
        return iterator_to_array($this->publishersUrisTable->getURIsForPublisher($publisherId));
    }

    /**
     * Retrieve an existing entry using a publisher ID and URI (null if not found).
     *
     * @param int    $publisherId Publisher ID
     * @param string $uri         URI
     *
     * @return ?PublishersUriEntityInterface
     */
    public function getByPublisherAndUri(int $publisherId, string $uri): ?PublishersUriEntityInterface
    {
        foreach ($this->publishersUrisTable->select(['Publisher_ID' => $publisherId, 'URI' => $uri]) as $row) {
            return $row;
        }
        return null;
    }
}
