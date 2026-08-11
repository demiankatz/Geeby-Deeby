<?php

/**
 * Interface for edition entity models.
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2025.
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

/**
 * Interface for edition entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface EditionEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the edition.
     *
     * @return string
     */
    public function getEditionName(): string;

    /**
     * Set the name of the edition.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setEditionName(string $name): static;

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface;

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static;

    /**
     * Get associated series (if any).
     *
     * @return ?SeriesEntityInterface
     */
    public function getSeries(): ?SeriesEntityInterface;

    /**
     * Set associated series (if any).
     *
     * @param int|SeriesEntityInterface|null $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface|null $series): static;

    /**
     * Get volume number.
     *
     * @return int
     */
    public function getVolume(): int;

    /**
     * Set volume number.
     *
     * @param int $volume Volume number
     *
     * @return static
     */
    public function setVolume(int $volume): static;

    /**
     * Get position in series.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set position in series.
     *
     * @param int $position Position in series
     *
     * @return static
     */
    public function setPosition(int $position): static;

    /**
     * Get replacement number (to disambiguate items in same position).
     *
     * @return int
     */
    public function getReplacementNumber(): int;

    /**
     * Set replacement number (to disambiguate items in same position).
     *
     * @param int $number Replacement number
     *
     * @return static
     */
    public function setReplacementNumber(int $number): static;

    /**
     * Get preferred item alternate title (if any).
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getPreferredItemAlternateTitle(): ?ItemsAltTitleEntityInterface;

    /**
     * Set preferred item alternate title (if any).
     *
     * @param int|ItemsAltTitleEntityInterface|null $altTitle Preferred alternate title
     *
     * @return static
     */
    public function setPreferredItemAlternateTitle(int|ItemsAltTitleEntityInterface|null $altTitle): static;

    /**
     * Get preferred series alternate title (if any).
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getPreferredSeriesAlternateTitle(): ?SeriesAltTitleEntityInterface;

    /**
     * Set preferred series alternate title (if any).
     *
     * @param int|SeriesAltTitleEntityInterface|null $altTitle Preferred alternate title
     *
     * @return static
     */
    public function setPreferredSeriesAlternateTitle(int|SeriesAltTitleEntityInterface|null $altTitle): static;

    /**
     * Get the length of the edition.
     *
     * @return ?string
     */
    public function getLength(): ?string;

    /**
     * Set the length of the edition.
     *
     * @param ?string $length New length value (null to clear)
     *
     * @return static
     */
    public function setLength(?string $length): static;

    /**
     * Get the ending count of the edition.
     *
     * @return ?string
     */
    public function getEndings(): ?string;

    /**
     * Set the ending count of the edition.
     *
     * @param ?string $endings New ending count value (null to clear)
     *
     * @return static
     */
    public function setEndings(?string $endings): static;

    /**
     * Get the description of the edition.
     *
     * @return ?string
     */
    public function getDescription(): ?string;

    /**
     * Set the description of the edition.
     *
     * @param ?string $desc New description.
     *
     * @return static
     */
    public function setDescription(?string $desc): static;

    /**
     * Get the link to the preferred publisher (if any).
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getPreferredPublisher(): ?SeriesPublisherEntityInterface;

    /**
     * Set the link to the preferred publisher (if any).
     *
     * @param int|SeriesPublisherEntityInterface|null $publisher New publisher link (null for none)
     *
     * @return static
     */
    public function setPreferredPublisher(int|SeriesPublisherEntityInterface|null $publisher): static;

    /**
     * Get parent edition (if any).
     *
     * @return ?EditionEntityInterface
     */
    public function getParentEdition(): ?EditionEntityInterface;

    /**
     * Set parent edition (if any).
     *
     * @param int|EditionEntityInterface|null $parent New parent edition (null for none)
     *
     * @return static
     */
    public function setParentEdition(int|EditionEntityInterface|null $parent): static;

    /**
     * Get position in parent edition.
     *
     * @return ?int
     */
    public function getPositionInParent(): ?int;

    /**
     * Set position in parent edition.
     *
     * @param int $position Position in parent edition
     *
     * @return static
     */
    public function setPositionInParent(?int $position): static;

    /**
     * Get the extent of this edition inside its parent.
     *
     * @return ?string
     */
    public function getExtentInParent(): ?string;

    /**
     * Set the extent of this edition inside its parent.
     *
     * @param ?string $extent New extent
     *
     * @return static
     */
    public function setExtentInParent(?string $extent): static;

    /**
     * Get display order within the edition list in the item.
     *
     * @return int
     */
    public function getItemDisplayOrder(): int;

    /**
     * Set display order within the edition list in the item.
     *
     * @param int $position Position in item
     *
     * @return static
     */
    public function setItemDisplayOrder(int $position): static;

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string;
}
