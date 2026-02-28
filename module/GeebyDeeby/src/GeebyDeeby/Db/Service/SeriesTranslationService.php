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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesTranslationEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\SeriesTranslations;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param EntityManager      $entityManager           Entity manager
     * @param PersistenceManager $persistenceManager      Persistence manager
     * @param SeriesTranslations $seriesTranslationsTable SeriesTranslations table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected SeriesTranslations $seriesTranslationsTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return SeriesTranslationEntityInterface
     */
    public function createEntity(): SeriesTranslationEntityInterface
    {
        return $this->seriesTranslationsTable->createRow();
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
        return iterator_to_array($this->seriesTranslationsTable->getTranslatedFrom($seriesID));
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
        return iterator_to_array($this->seriesTranslationsTable->getTranslatedInto($seriesID));
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
        $where = [
            'Source_Series_ID' => $source instanceof SeriesEntityInterface ? $source->getId() : $source,
            'Trans_Series_ID' => $translated instanceof SeriesEntityInterface ? $translated->getId() : $translated,
        ];
        foreach ($this->seriesTranslationsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
