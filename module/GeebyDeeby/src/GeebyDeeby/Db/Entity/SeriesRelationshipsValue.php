<?php

/**
 * Series relationship value entity model.
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
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Series relationship value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Relationships_Values')]
#[ORM\Index(name: 'idx_6d78e13713911185', columns: ['Subject_Series_ID'])]
#[ORM\Index(name: 'series_relationship_id', columns: ['Series_Relationship_ID'])]
#[ORM\Index(name: 'object_series_id', columns: ['Object_Series_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesRelationshipsValue extends AbstractEntity implements SeriesRelationshipsValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Subject series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Subject_Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $subjectSeries;

    /**
     * Relationship.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Series_Relationship_ID',
        referencedColumnName: 'Series_Relationship_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: SeriesRelationship::class)]
    protected SeriesRelationship $relationship;

    /**
     * Object series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Object_Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $objectSeries;

    /**
     * Get subject series.
     *
     * @return SeriesEntityInterface
     */
    public function getSubject(): SeriesEntityInterface
    {
        return $this->subjectSeries;
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
        if ($series instanceof Series) {
            $this->subjectSeries = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->subjectSeries = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get relationship.
     *
     * @return SeriesRelationshipEntityInterface
     */
    public function getRelationship(): SeriesRelationshipEntityInterface
    {
        return $this->relationship;
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
        if ($relationship instanceof SeriesRelationship) {
            $this->relationship = $relationship;
            return $this;
        } elseif ($relationship instanceof SeriesRelationshipEntityInterface) {
            $relationship = $relationship->getId();
        }
        $this->relationship = $this->entityManager->getReference(SeriesRelationship::class, $relationship);
        return $this;
    }

    /**
     * Get object series.
     *
     * @return SeriesEntityInterface
     */
    public function getObject(): SeriesEntityInterface
    {
        return $this->objectSeries;
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
        if ($series instanceof Series) {
            $this->objectSeries = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->objectSeries = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }
}
