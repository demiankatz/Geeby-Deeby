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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesReviewEntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesReviews;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param EntityManager      $entityManager      Entity manager
     * @param PersistenceManager $persistenceManager Persistence manager
     * @param SeriesReviews      $seriesReviewsTable SeriesReviews table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesReviews $seriesReviewsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesReviewEntityInterface
     */
    public function createEntity(): SeriesReviewEntityInterface
    {
        return $this->seriesReviewsTable->createRow();
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
        return iterator_to_array($this->seriesReviewsTable->getReviewsForSeries($seriesID, $approved));
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
        return iterator_to_array($this->seriesReviewsTable->getReviewsByUser($userID, $approved));
    }

    /**
     * Get recent series comments.
     *
     * @return array
     */
    public function getRecentSeriesComments(): array
    {
        return iterator_to_array($this->seriesReviewsTable->getRecentSeriesComments());
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
        $where = [
            'User_ID' => $user instanceof UserEntityInterface ? $user->getId() : $user,
            'Series_ID' => $series instanceof SeriesEntityInterface ? $series->getId() : $series,
        ];
        foreach ($this->seriesReviewsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
