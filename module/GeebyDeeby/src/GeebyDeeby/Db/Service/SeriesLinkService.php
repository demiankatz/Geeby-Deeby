<?php

/**
 * Database service for the Series_Links table.
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
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesLinkEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesLinks;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Links table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesLinkService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param SeriesLinks        $seriesLinksTable   SeriesLinks table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesLinks $seriesLinksTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesLinkEntityInterface
     */
    public function createEntity(): SeriesLinkEntityInterface
    {
        return $this->seriesLinksTable->createRow();
    }

    /**
     * Get a list of series for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return array
     */
    public function getSeriesForLink(int $linkID): array
    {
        return iterator_to_array($this->seriesLinksTable->getSeriesForLink($linkID));
    }

    /**
     * Get a list of links for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getLinksForSeries(int $seriesID): array
    {
        return iterator_to_array($this->seriesLinksTable->getLinksForSeries($seriesID));
    }

    /**
     * Retrieve the record for the specified link and series.
     *
     * @param int|LinkEntityInterface   $link   Link ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     *
     * @return ?SeriesLinkEntityInterface
     */
    public function getForLinkAndSeries(
        int|LinkEntityInterface $link,
        int|SeriesEntityInterface $series
    ): ?SeriesLinkEntityInterface {
        $where = [
            'Link_ID' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        foreach ($this->seriesLinksTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
