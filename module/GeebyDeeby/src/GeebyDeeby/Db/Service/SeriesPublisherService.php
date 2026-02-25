<?php

/**
 * Database service for the Series_Publishers table.
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
use GeebyDeeby\Db\Entity\Note;
use GeebyDeeby\Db\Entity\Publisher;
use GeebyDeeby\Db\Entity\PublishersAddress;
use GeebyDeeby\Db\Entity\PublishersImprint;
use GeebyDeeby\Db\Entity\Series;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesPublisher;
use GeebyDeeby\Db\Entity\SeriesPublisherEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Series_Publishers table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesPublisherService extends AbstractDbService
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
     * @return SeriesPublisherEntityInterface
     */
    public function createEntity(): SeriesPublisherEntityInterface
    {
        $entity = new SeriesPublisher();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of series for the specified city.
     *
     * @param int $cityID City ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForCity(int $cityID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s'
            . ' INNER JOIN ' . SeriesPublisher::class . ' sp ON s.id = sp.series '
            . 'INNER JOIN ' . PublishersAddress::class . ' pa ON sp.address = pa.id '
            . 'WHERE pa.city = :city '
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('city', $cityID);
        return $query->getResult();
    }

    /**
     * Get a list of series for the specified country.
     *
     * @param int $countryID Country ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForCountry(int $countryID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s'
            . ' INNER JOIN ' . SeriesPublisher::class . ' sp ON s.id = sp.series '
            . 'INNER JOIN ' . PublishersAddress::class . ' pa ON sp.address = pa.id '
            . 'WHERE pa.country = :country '
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('country', $countryID);
        return $query->getResult();
    }

    /**
     * Get a list of series for the specified address.
     *
     * @param int $addressID Address ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForAddress(int $addressID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s'
            . ' INNER JOIN ' . SeriesPublisher::class . ' sp ON s.id = sp.series '
            . 'WHERE sp.address = :address '
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('address', $addressID);
        return $query->getResult();
    }

    /**
     * Get a list of series for the specified imprint.
     *
     * @param int $imprintID Imprint ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForImprint(int $imprintID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s'
            . ' INNER JOIN ' . SeriesPublisher::class . ' sp ON s.id = sp.series '
            . 'WHERE sp.imprint = :imprint '
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('imprint', $imprintID);
        return $query->getResult();
    }

    /**
     * Get a list of series for the specified publisher.
     *
     * @param int $publisherID Publisher ID
     *
     * @return SeriesEntityInterface[]
     */
    public function getSeriesForPublisher(int $publisherID): array
    {
        $dql = 'SELECT DISTINCT s FROM ' . Series::class . ' s'
            . ' INNER JOIN ' . SeriesPublisher::class . ' sp ON s.id = sp.series '
            . 'WHERE sp.publisher = :publisher '
            . 'ORDER BY s.seriesName, s.id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('publisher', $publisherID);
        return $query->getResult();
    }

    /**
     * Get a list of publishers for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getPublishersForSeries(int $seriesID): array
    {
        $dql = 'SELECT sp.id AS Series_Publisher_ID, p.id AS Publisher_ID, p.publisherName as Publisher_Name, '
            . 'pa.id AS Address_ID, pa.street AS Street, '
            . 'ci.id AS City_ID, ci.cityName AS City_Name, '
            . 'co.id AS Country_ID, co.countryName AS Country_Name, '
            . 'n.id AS Note_ID, n.note AS Note, '
            . 'i.id AS Imprint_ID, i.imprintName AS Imprint_Name '
            . 'FROM ' . SeriesPublisher::class . ' sp '
            . 'LEFT JOIN ' . Publisher::class . ' p ON sp.publisher = p.id '
            . 'LEFT JOIN ' . PublishersAddress::class . ' pa ON sp.address = pa.id '
            . 'LEFT JOIN ' . Country::class . ' co ON pa.country = co.id '
            . 'LEFT JOIN ' . City::class . ' ci ON pa.city = ci.id '
            . 'LEFT JOIN ' . Note::class . ' n ON sp.note = n.id '
            . 'LEFT JOIN ' . PublishersImprint::class . ' i ON sp.imprint = i.id '
            . 'WHERE sp.series = :series '
            . 'ORDER BY p.publisherName, i.imprintName, co.countryName, ci.cityName, pa.street';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Retrieve an entity using its primary key (null if not found).
     *
     * @param int $id Primary key value
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getByPrimaryKey(int $id): ?SeriesPublisherEntityInterface
    {
        return $this->entityManager->find(SeriesPublisher::class, $id);
    }

    /**
     * Retrieve an existing entry using an series ID and sequence ID (null if not found).
     *
     * @param int    $seriesId Series ID
     * @param string $spId     Series/publisher primary key
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getBySeriesAndId(int $seriesId, string $spId): ?SeriesPublisherEntityInterface
    {
        $dql = 'SELECT sp FROM ' . SeriesPublisher::class . ' sp WHERE sp.series = :series AND sp.id = :id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['series' => $seriesId, 'id' => $spId]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
