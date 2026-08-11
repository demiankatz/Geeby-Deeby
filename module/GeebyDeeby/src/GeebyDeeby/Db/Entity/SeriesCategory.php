<?php

/**
 * Series category entity model.
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
 * Series category entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Categories')]
#[ORM\Index(name: 'idx_224123107e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'category_id', columns: ['Category_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesCategory extends AbstractEntity implements SeriesCategoryEntityInterface
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
     * Category.
     *
     * @var Category
     */
    #[ORM\JoinColumn(
        name: 'Category_ID',
        referencedColumnName: 'Category_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Category::class)]
    protected Category $category;

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
     * Get associated category.
     *
     * @return CategoryEntityInterface
     */
    public function getCategory(): CategoryEntityInterface
    {
        return $this->category;
    }

    /**
     * Set associated category.
     *
     * @param int|CategoryEntityInterface $category Associated category entity or ID
     *
     * @return static
     */
    public function setCategory(int|CategoryEntityInterface $category): static
    {
        if ($category instanceof Category) {
            $this->category = $category;
            return $this;
        } elseif ($category instanceof CategoryEntityInterface) {
            $category = $category->getId();
        }
        $this->category = $this->entityManager->getReference(Category::class, $category);
        return $this;
    }
}
