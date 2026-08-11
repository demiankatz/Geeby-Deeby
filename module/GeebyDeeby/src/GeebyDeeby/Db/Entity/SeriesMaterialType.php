<?php

/**
 * Series material type entity model.
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
 * Series material type entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Material_Types')]
#[ORM\Index(name: 'idx_eb1edfae7e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'material_type_id', columns: ['Material_Type_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesMaterialType extends AbstractEntity implements SeriesMaterialTypeEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $series;

    /**
     * Material type.
     *
     * @var MaterialType
     */
    #[ORM\JoinColumn(
        name: 'Material_Type_ID',
        referencedColumnName: 'Material_Type_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: MaterialType::class)]
    protected MaterialType $materialType;

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->series;
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static
    {
        if ($series instanceof Series) {
            $this->series = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->series = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get associated material type.
     *
     * @return MaterialTypeEntityInterface
     */
    public function getMaterialType(): MaterialTypeEntityInterface
    {
        return $this->materialType;
    }

    /**
     * Set associated material type.
     *
     * @param int|MaterialTypeEntityInterface $materialType Associated material type entity or ID
     *
     * @return static
     */
    public function setMaterialType(int|MaterialTypeEntityInterface $materialType): static
    {
        if ($materialType instanceof MaterialType) {
            $this->materialType = $materialType;
            return $this;
        } elseif ($materialType instanceof MaterialTypeEntityInterface) {
            $materialType = $materialType->getId();
        }
        $this->materialType = $this->entityManager->getReference(MaterialType::class, $materialType);
        return $this;
    }
}
