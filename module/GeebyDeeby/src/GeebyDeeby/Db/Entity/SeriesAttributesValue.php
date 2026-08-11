<?php

/**
 * Series attribute value entity model.
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
 * Series attribute value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Attributes_Values')]
#[ORM\Index(name: 'idx_9969a42f7e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'series_attribute_id', columns: ['Series_Attribute_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesAttributesValue extends AbstractEntity implements SeriesAttributesValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Series.
     *
     * @var Series
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $series;

    /**
     * Series attribute.
     *
     * @var SeriesAttribute
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Series_Attribute_ID',
        referencedColumnName: 'Series_Attribute_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: SeriesAttribute::class)]
    protected SeriesAttribute $attribute;

    /**
     * Attribute value.
     *
     * @var string
     */
    #[ORM\Column(name: 'Series_Attribute_Value', type: 'text', length: 16777215, nullable: false)]
    protected string $value;

    /**
     * Get associated series.
     *
     * @return ?SeriesEntityInterface
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
    public function setSeries(int|SeriesEntityInterface|null $series): static
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
     * Get associated attribute.
     *
     * @return ?SeriesAttributeEntityInterface
     */
    public function getAttribute(): SeriesAttributeEntityInterface
    {
        return $this->attribute;
    }

    /**
     * Set associated attribute.
     *
     * @param int|SeriesAttributeEntityInterface $attribute Associated series entity or ID
     *
     * @return static
     */
    public function setAttribute(int|SeriesAttributeEntityInterface|null $attribute): static
    {
        if ($attribute instanceof SeriesAttribute) {
            $this->attribute = $attribute;
            return $this;
        } elseif ($attribute instanceof SeriesAttributeEntityInterface) {
            $attribute = $attribute->getId();
        }
        $this->attribute = $this->entityManager->getReference(SeriesAttribute::class, $attribute);
        return $this;
    }

    /**
     * Get the value of the attribute.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the value of the attribute.
     *
     * @param string $value New value
     *
     * @return static
     */
    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }
}
