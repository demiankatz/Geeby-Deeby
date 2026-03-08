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
#[ORM\Table(name: 'Series_Translations')]
#[ORM\Index(name: 'idx_1c89a41c1c25fe6', columns: ['Source_Series_ID'])]
#[ORM\Index(name: 'trans_series_id', columns: ['Trans_Series_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesTranslation extends AbstractEntity implements SeriesTranslationEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Source series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Source_Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $sourceSeries;

    /**
     * Translated series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Trans_Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $translatedSeries;

    /**
     * Get source series.
     *
     * @return SeriesEntityInterface
     */
    public function getSourceSeries(): SeriesEntityInterface
    {
        return $this->sourceSeries;
    }

    /**
     * Set source series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSourceSeries(int|SeriesEntityInterface $series): static
    {
        if ($series instanceof Series) {
            $this->sourceSeries = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->sourceSeries = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get translated series.
     *
     * @return SeriesEntityInterface
     */
    public function getTranslatedSeries(): SeriesEntityInterface
    {
        return $this->translatedSeries;
    }

    /**
     * Set translated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setTranslatedSeries(int|SeriesEntityInterface $series): static
    {
        if ($series instanceof Series) {
            $this->translatedSeries = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->translatedSeries = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }
}
