<?php

/**
 * Row Definition for Series_Relationships_Values
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\SeriesRelationshipsValueEntityInterface;

/**
 * Row Definition for Series_Relationships_Values
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesRelationshipsValues extends TableAwareGateway implements SeriesRelationshipsValueEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(
            ['Subject_Series_ID', 'Series_Relationship_ID', 'Object_Series_ID'],
            'Series_Relationships_Values',
            $adapter
        );
    }

    /**
     * Get subject series.
     *
     * @return SeriesEntityInterface
     */
    public function getSubject(): SeriesEntityInterface
    {
        return $this->getTableManager()->get('series')->getByPrimaryKey($this->Subject_Series_ID);
    }

    /**
     * Set subject series.
     *
     * @param int|SeriesEntityInterface $series Subject series entity or ID
     *
     * @return static
     */
    public function setSubject(int|SeriesEntityInterface $series): static
    {
        $this->Subject_Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return SeriesRelationshipEntityInterface
     */
    public function getRelationship(): SeriesRelationshipEntityInterface
    {
        return $this->getTableManager()->get('seriesrelationship')->getByPrimaryKey($this->Series_Relationship_ID);
    }

    /**
     * Set relationship.
     *
     * @param int|SeriesRelationshipEntityInterface $relationship Relationship entity or ID
     *
     * @return static
     */
    public function setRelationship(int|SeriesRelationshipEntityInterface $relationship): static
    {
        $this->Series_Relationship_ID = $relationship instanceof SeriesRelationshipEntityInterface
            ? $relationship->getId() : $relationship;
        return $this;
    }

    /**
     * Get object series.
     *
     * @return SeriesEntityInterface
     */
    public function getObject(): SeriesEntityInterface
    {
        return $this->getTableManager()->get('series')->getByPrimaryKey($this->Object_Series_ID);
    }

    /**
     * Set object series.
     *
     * @param int|SeriesEntityInterface $series Object series entity or ID
     *
     * @return static
     */
    public function setObject(int|SeriesEntityInterface $series): static
    {
        $this->Object_Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }
}
