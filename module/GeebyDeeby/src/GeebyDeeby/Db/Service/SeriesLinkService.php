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

use GeebyDeeby\Db\Entity\Link;
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesLink;
use GeebyDeeby\Db\Entity\SeriesLinkEntityInterface;

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
     * Create an empty entity.
     *
     * @return SeriesLinkEntityInterface
     */
    public function createEntity(): SeriesLinkEntityInterface
    {
        $entity = new SeriesLink();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of series for the specified link.
     *
     * @param int $linkID Link ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForLink(int $linkID): array
    {
        $dql = 'SELECT s FROM ' . SeriesLink::class . ' sl '
            . 'INNER JOIN ' . Series::class . ' s ON sl.series=s.id '
            . 'WHERE sl.link = :link ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('link', $linkID);
        return $query->getResult();
    }

    /**
     * Get a list of links for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return LinkEntityInterface[]
     */
    public function getLinksForSeries(int $seriesID): array
    {
        $dql = 'SELECT l FROM ' . SeriesLink::class . ' sl '
            . 'INNER JOIN ' . Link::class . ' l ON sl.link=l.id '
            . 'WHERE sl.series = :series ORDER BY l.linkName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
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
        $params = [
            'link' => $link instanceof LinkEntityInterface ? $link->getId() : $link,
            'series' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        $dql = 'SELECT sl FROM ' . SeriesLink::class . ' sl WHERE sl.series = :series AND sl.link = :link';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
