<?php

/**
 * Row Definition for Editions
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\ItemEntityInterface;
use GeebyDeeby\Db\Entity\ItemsAltTitleEntityInterface;
use GeebyDeeby\Db\Entity\SeriesAltTitleEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesPublisherEntityInterface;

use function count;

/**
 * Row Definition for Editions
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Edition extends TableAwareGateway implements EditionEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Edition_ID', 'Editions', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Edition_ID ?? null;
    }

    /**
     * Get the name of the edition.
     *
     * @return string
     */
    public function getEditionName(): string
    {
        return $this->Edition_Name;
    }

    /**
     * Set the name of the edition.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setEditionName(string $name): static
    {
        $this->Edition_Name = $name;
        return $this;
    }

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->getTableManager()->get('item')->getByPrimaryKey($this->Item_ID);
    }

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static
    {
        $this->Item_ID = $item instanceof ItemEntityInterface ? $item->getId() : $item;
        return $this;
    }

    /**
     * Get associated series (if any).
     *
     * @return ?SeriesEntityInterface
     */
    public function getSeries(): ?SeriesEntityInterface
    {
        return $this->Series_ID ? $this->getTableManager()->get('series')->getByPrimaryKey($this->Series_ID) : null;
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface|null $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface|null $series): static
    {
        $this->Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }

    /**
     * Get volume number.
     *
     * @return int
     */
    public function getVolume(): int
    {
        return $this->Volume;
    }

    /**
     * Set volume number.
     *
     * @param int $volume Volume number
     *
     * @return static
     */
    public function setVolume(int $volume): static
    {
        $this->Volume = $volume;
        return $this;
    }

    /**
     * Get position in series.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->Position;
    }

    /**
     * Set position in series.
     *
     * @param int $position Position in series
     *
     * @return static
     */
    public function setPosition(int $position): static
    {
        $this->Position = $position;
        return $this;
    }

    /**
     * Get replacement number (to disambiguate items in same position).
     *
     * @return int
     */
    public function getReplacementNumber(): int
    {
        return $this->Replacement_Number;
    }

    /**
     * Set replacement number (to disambiguate items in same position).
     *
     * @param int $number Replacement number
     *
     * @return static
     */
    public function setReplacementNumber(int $number): static
    {
        $this->Replacement_Number = $number;
        return $this;
    }

    /**
     * Get preferred item alternate title (if any).
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getPreferredItemAlternateTitle(): ?ItemsAltTitleEntityInterface
    {
        return $this->Preferred_Item_AltName_ID
            ? $this->getTableManager()->get('itemsalttitles')->getByPrimaryKey($this->Preferred_Item_AltName_ID)
            : null;
    }

    /**
     * Set preferred item alternate title (if any).
     *
     * @param int|ItemsAltTitleEntityInterface|null $altTitle Preferred alternate title
     *
     * @return static
     */
    public function setPreferredItemAlternateTitle(int|ItemsAltTitleEntityInterface|null $altTitle): static
    {
        $this->Preferred_Item_AltName_ID = $altTitle instanceof ItemsAltTitleEntityInterface
            ? $altTitle->getId() : $altTitle;
        return $this;
    }

    /**
     * Get preferred series alternate title (if any).
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getPreferredSeriesAlternateTitle(): ?SeriesAltTitleEntityInterface
    {
        return $this->Preferred_Series_AltName_ID
            ? $this->getTableManager()->get('seriesalttitles')->getByPrimaryKey($this->Preferred_Series_AltName_ID)
            : null;
    }

    /**
     * Set preferred series alternate title (if any).
     *
     * @param int|SeriesAltTitleEntityInterface|null $altTitle Preferred alternate title
     *
     * @return static
     */
    public function setPreferredSeriesAlternateTitle(int|SeriesAltTitleEntityInterface|null $altTitle): static
    {
        $this->Preferred_Series_AltName_ID = $altTitle instanceof SeriesAltTitleEntityInterface
            ? $altTitle->getId() : $altTitle;
        return $this;
    }

    /**
     * Get the length of the edition.
     *
     * @return ?string
     */
    public function getLength(): ?string
    {
        return $this->Edition_Length;
    }

    /**
     * Set the length of the edition.
     *
     * @param ?string $length New length value (null to clear)
     *
     * @return static
     */
    public function setLength(?string $length): static
    {
        $this->Edition_Length = $length;
        return $this;
    }

    /**
     * Get the ending count of the edition.
     *
     * @return ?string
     */
    public function getEndings(): ?string
    {
        return $this->Edition_Endings;
    }

    /**
     * Set the ending count of the edition.
     *
     * @param ?string $endings New ending count value (null to clear)
     *
     * @return static
     */
    public function setEndings(?string $endings): static
    {
        $this->Edition_Endings = $endings;
        return $this;
    }

    /**
     * Get the description of the edition.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->Edition_Description;
    }

    /**
     * Set the description of the edition.
     *
     * @param ?string $desc New description.
     *
     * @return static
     */
    public function setDescription(?string $desc): static
    {
        $this->Edition_Description = $desc;
        return $this;
    }

    /**
     * Get the link to the preferred publisher (if any).
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getPreferredPublisher(): ?SeriesPublisherEntityInterface
    {
        return $this->Preferred_Series_Publisher_ID
            ? $this->getTableManager()->get('seriespublishers')->getByPrimaryKey($this->Preferred_Series_Publisher_ID)
            : null;
    }

    /**
     * Set the link to the preferred publisher (if any).
     *
     * @param int|SeriesPublisherEntityInterface|null $publisher New publisher link (null for none)
     *
     * @return static
     */
    public function setPreferredPublisher(int|SeriesPublisherEntityInterface|null $publisher): static
    {
        $this->Preferred_Series_Publisher_ID = $publisher instanceof SeriesPublisherEntityInterface
            ? $publisher->getId() : $publisher;
        return $this;
    }

    /**
     * Get parent edition (if any).
     *
     * @return ?EditionEntityInterface
     */
    public function getParentEdition(): ?EditionEntityInterface
    {
        return $this->Parent_Edition_ID
            ? $this->getTableManager()->get('edition')->getByPrimaryKey($this->Parent_Edition_ID)
            : null;
    }

    /**
     * Set parent edition (if any).
     *
     * @param int|EditionEntityInterface|null $parent New parent edition (null for none)
     *
     * @return static
     */
    public function setParentEdition(int|EditionEntityInterface|null $parent): static
    {
        $this->Parent_Edition_ID = $parent instanceof EditionEntityInterface
            ? $parent->getId() : $parent;
        return $this;
    }

    /**
     * Get position in parent edition.
     *
     * @return ?int
     */
    public function getPositionInParent(): ?int
    {
        return $this->Position_In_Parent;
    }

    /**
     * Set position in parent edition.
     *
     * @param int $position Position in parent edition
     *
     * @return static
     */
    public function setPositionInParent(?int $position): static
    {
        $this->Position_In_Parent = $position;
        return $this;
    }

    /**
     * Get the extent of this edition inside its parent.
     *
     * @return ?string
     */
    public function getExtentInParent(): ?string
    {
        return $this->Extent_In_Parent;
    }

    /**
     * Set the extent of this edition inside its parent.
     *
     * @param ?string $extent New extent
     *
     * @return static
     */
    public function setExtentInParent(?string $extent): static
    {
        $this->Extent_In_Parent = $extent;
        return $this;
    }

    /**
     * Get display order within the edition list in the item.
     *
     * @return int
     */
    public function getItemDisplayOrder(): int
    {
        return $this->Item_Display_Order;
    }

    /**
     * Set display order within the edition list in the item.
     *
     * @param int $position Position in item
     *
     * @return static
     */
    public function setItemDisplayOrder(int $position): static
    {
        $this->Item_Display_Order = $position;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->Edition_Name;
    }
}
