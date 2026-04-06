<?php

/**
 * Database service for the Series_Reviews table.
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

use GeebyDeeby\Db\Entity\Enum\Approved;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesReview;
use GeebyDeeby\Db\Entity\SeriesReviewEntityInterface;
use GeebyDeeby\Db\Entity\User;
use GeebyDeeby\Db\Entity\UserEntityInterface;

/**
 * Database service for the Series_Reviews table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesReviewService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesReviewEntityInterface
     */
    public function createEntity(): SeriesReviewEntityInterface
    {
        $entity = new SeriesReview();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of reviews for the specified series.
     *
     * @param int     $seriesID Series ID
     * @param ?string $approved 'y' to get only approved series, 'n' for only
     * unapproved series, null for all series
     *
     * @return array
     */
    public function getReviewsForSeries(int $seriesID, ?string $approved = 'y'): array
    {
        $params = ['series' => $seriesID];
        $dql = 'SELECT r.review AS Review, r.added as Added, u.id AS User_ID, u.username AS Username, u.name AS Name '
            . 'FROM ' . SeriesReview::class . ' r '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . 'WHERE r.series=:series';
        if ($approved) {
            $dql .= ' AND r.approved=:approved';
            $params['approved'] = Approved::from($approved);
        }
        $dql .= ' ORDER BY u.username';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get a list of series reviewed by the specified user.
     *
     * @param ?int    $userID   User ID (null for all users)
     * @param ?string $approved 'y' to get only approved series, 'n' for only
     * unapproved series, null for all series
     *
     * @return array
     */
    public function getReviewsByUser(?int $userID, ?string $approved = 'y'): array
    {
        $params = $where = [];
        if ($approved) {
            $where[] = 'r.approved=:approved';
            $params['approved'] = Approved::from($approved);
        }
        if ($userID) {
            $where[] = 'r.user=:user';
            $params['user'] = $userID;
        }
        $dql = 'SELECT r.review AS Review, r.added as Added, '
            . 'u.id AS User_ID, u.username AS Username, u.name AS Name, s.id AS Series_ID, s.seriesName AS Series_Name '
            . 'FROM ' . SeriesReview::class . ' r '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . 'INNER JOIN ' . Series::class . ' s ON r.series=s.id '
            . ($where ? 'WHERE ' . implode(' AND ', $where) . ' ' : '')
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        return $query->getResult();
    }

    /**
     * Get recent series comments.
     *
     * @return array
     */
    public function getRecentSeriesComments(): array
    {
        $dql = 'SELECT r.review AS Review, r.added as Added, u.id AS User_ID, u.username AS Username, u.name AS Name, '
            . 's.id AS Series_ID, s.seriesName AS Series_Name '
            . 'FROM ' . SeriesReview::class . ' r '
            . 'INNER JOIN ' . User::class . ' u ON r.user=u.id '
            . 'INNER JOIN ' . Series::class . ' s ON r.series=s.id '
            . 'WHERE r.approved=:approved ORDER BY r.added DESC, u.username';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('approved', Approved::Yes);
        return $query->getResult();
    }

    /**
     * Get a row matching the provided series and source.
     *
     * @param int|UserEntityInterface   $user   User ID or entity
     * @param int|SeriesEntityInterface $series Series ID or entity
     *
     * @return ?SeriesReviewEntityInterface
     */
    public function getByUserAndSeries(
        int|UserEntityInterface $user,
        int|SeriesEntityInterface $series
    ): ?SeriesReviewEntityInterface {
        $params = [
            'user' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'series' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        $dql = 'SELECT r FROM ' . SeriesReview::class . ' r WHERE r.series = :series AND r.user = :user';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
