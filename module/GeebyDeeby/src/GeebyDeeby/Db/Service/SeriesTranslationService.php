<?php

/**
 * Database service for the Series_Translations table.
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

use GeebyDeeby\Db\Entity\Language;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesTranslation;
use GeebyDeeby\Db\Entity\SeriesTranslationEntityInterface;

/**
 * Database service for the Series_Translations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesTranslationService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesTranslationEntityInterface
     */
    public function createEntity(): SeriesTranslationEntityInterface
    {
        $entity = new SeriesTranslation();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of series translated from the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getTranslatedFrom(int $seriesID): array
    {
        $dql = 'SELECT s.id AS Series_ID, s.seriesName AS Series_Name, '
            . 'l.id AS Language_ID, l.languageName AS Language_Name '
            . 'FROM ' . SeriesTranslation::class . ' st '
            . 'INNER JOIN ' . Series::class . ' s ON st.translatedSeries=s.id '
            . 'INNER JOIN ' . Language::class . ' l ON s.language=l.id '
            . 'WHERE st.sourceSeries=:series ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Get a list of series translated into the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getTranslatedInto(int $seriesID): array
    {
        $dql = 'SELECT s.id AS Series_ID, s.seriesName AS Series_Name, '
            . 'l.id AS Language_ID, l.languageName AS Language_Name '
            . 'FROM ' . SeriesTranslation::class . ' st '
            . 'INNER JOIN ' . Series::class . ' s ON st.sourceSeries=s.id '
            . 'INNER JOIN ' . Language::class . ' l ON s.language=l.id '
            . 'WHERE st.translatedSeries=:series ORDER BY s.seriesName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Get a row matching the provided source series/translated series pair.
     *
     * @param int|SeriesEntityInterface $source     Source series ID or entity
     * @param int|SeriesEntityInterface $translated Translated series ID or entity
     *
     * @return ?SeriesTranslationEntityInterface
     */
    public function getBySourceSeriesAndTranslatedSeries(
        int|SeriesEntityInterface $source,
        int|SeriesEntityInterface $translated
    ): ?SeriesTranslationEntityInterface {
        $dql = 'SELECT st FROM ' . SeriesTranslation::class
            . ' st WHERE st.sourceSeries=:source AND st.translatedSeries=:translated';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('source', $source instanceof SeriesEntityInterface ? $source->getId() : $source);
        $query->setParameter(
            'translated',
            $translated instanceof SeriesEntityInterface ? $translated->getId() : $translated
        );
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
