<?php

/**
 * Database service for the Series_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\SeriesAttribute;
use GeebyDeeby\Db\Entity\SeriesAttributesValue;
use GeebyDeeby\Db\Entity\SeriesAttributesValueEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;

/**
 * Database service for the Series_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesAttributesValueService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return SeriesAttributesValueEntityInterface
     */
    public function createEntity(): SeriesAttributesValueEntityInterface
    {
        $entity = new SeriesAttributesValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of attributes for the specified series.
     *
     * @param int $seriesID Series ID
     *
     * @return array
     */
    public function getAttributesForSeries(int $seriesID): array
    {
        $dql = 'SELECT sav.value AS Series_Attribute_Value, sa.id AS Series_Attribute_ID, '
            . 'sa.attributeName AS Series_Attribute_Name, sa.rdfProperty AS Series_Attribute_RDF_Property, '
            . 'sa.allowHtml AS Allow_HTML, sa.displayPriority AS Display_Priority FROM '
            . SeriesAttributesValue::class . ' sav '
            . 'INNER JOIN ' . SeriesAttribute::class . ' sa ON sav.attribute=sa.id '
            . 'WHERE sav.series = :series ORDER BY sa.displayPriority, sa.attributeName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $seriesID);
        return $query->getResult();
    }

    /**
     * Delete existing attributes associated with the provided series.
     *
     * @param int|SeriesEntityInterface $series Series entity or ID
     *
     * @return void
     */
    public function deleteBySeries(int|SeriesEntityInterface $series): void
    {
        $dql = 'DELETE FROM ' . SeriesAttributesValue::class . ' sav WHERE sav.series=:series';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('series', $series instanceof SeriesEntityInterface ? $series->getId() : $series);
        $query->execute();
    }
}
