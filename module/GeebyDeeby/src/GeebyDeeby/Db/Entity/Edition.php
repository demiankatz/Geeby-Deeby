<?php

/**
 * Edition entity model.
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
 * Edition entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions')]
#[ORM\Index(name: 'item_id', columns: ['Item_ID'])]
#[ORM\Index(name: 'series_id', columns: ['Series_ID'])]
#[ORM\Index(name: 'preferred_item_altname_id', columns: ['Preferred_Item_AltName_ID'])]
#[ORM\Index(name: 'preferred_series_altname_id', columns: ['Preferred_Series_AltName_ID'])]
#[ORM\Index(name: 'preferred_series_publisher_id', columns: ['Preferred_Series_Publisher_ID'])]
#[ORM\Index(name: 'parent_edition_id', columns: ['Parent_Edition_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Edition extends AbstractEntity implements EditionEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Edition_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Edition name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Edition_Name', type: 'text', length: 255, nullable: false)]
    protected string $editionName;

    /**
     * Item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $item;

    /**
     * Series.
     *
     * @var ?Series
     */
    #[ORM\JoinColumn(
        name: 'Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected ?Series $series = null;

    /**
     * Volume.
     *
     * @var int
     */
    #[ORM\Column(name: 'Volume', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $volume = 0;

    /**
     * Position.
     *
     * @var int
     */
    #[ORM\Column(name: 'Position', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $position = 0;

    /**
     * Replacement number.
     *
     * @var int
     */
    #[ORM\Column(name: 'Replacement_Number', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $replacementNumber = 0;

    /**
     * Preferred item alternate name.
     *
     * @var ?ItemsAltTitle
     */
    #[ORM\JoinColumn(
        name: 'Preferred_Item_AltName_ID',
        referencedColumnName: 'Sequence_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: ItemsAltTitle::class)]
    protected ?ItemsAltTitle $preferredItemAltName = null;

    /**
     * Preferred series alternate name.
     *
     * @var ?SeriesAltTitle
     */
    #[ORM\JoinColumn(
        name: 'Preferred_Series_AltName_ID',
        referencedColumnName: 'Sequence_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: SeriesAltTitle::class)]
    protected ?SeriesAltTitle $preferredSeriesAltName = null;

    /**
     * Length.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Edition_Length', type: 'text', length: 255, nullable: true)]
    protected ?string $length;

    /**
     * Endings count.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Edition_Endings', type: 'text', length: 255, nullable: true)]
    protected ?string $endings;

    /**
     * Description.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Edition_Description', type: 'text', length: 65535, nullable: true)]
    protected ?string $description;

    /**
     * Preferred series/publisher link.
     *
     * @var ?SeriesPublisher
     */
    #[ORM\JoinColumn(
        name: 'Preferred_Series_Publisher_ID',
        referencedColumnName: 'Series_Publisher_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: SeriesPublisher::class)]
    protected ?SeriesPublisher $preferredSeriesPublisher = null;

    /**
     * Parent edition.
     *
     * @var ?Edition
     */
    #[ORM\JoinColumn(
        name: 'Parent_Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected ?Edition $parentEdition = null;

    /**
     * Position in parent.
     *
     * @var ?int
     */
    #[ORM\Column(name: 'Position_In_Parent', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $positionInParent = null;

    /**
     * Endings count.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Extent_In_Parent', type: 'text', length: 65535, nullable: true)]
    protected ?string $extentInParent;

    /**
     * Display order within item.
     *
     * @var int
     */
    #[ORM\Column(name: 'Item_Display_Order', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $itemDisplayOrder = 0;

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * Get the name of the Edition.
     *
     * @return string
     */
    public function getEditionName(): string
    {
        return $this->editionName;
    }

    /**
     * Set the name of the Edition.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setEditionName(string $name): static
    {
        $this->editionName = $name;
        return $this;
    }

    /**
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->item;
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
        if ($item instanceof Item) {
            $this->item = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->item = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }

    /**
     * Get associated series (if any).
     *
     * @return ?SeriesEntityInterface
     */
    public function getSeries(): ?SeriesEntityInterface
    {
        return $this->series;
    }

    /**
     * Set associated series (if any).
     *
     * @param int|SeriesEntityInterface|null $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface|null $series): static
    {
        if ($series instanceof Series || $series === null) {
            $this->series = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->series = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get volume number.
     *
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
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
        $this->volume = $volume;
        return $this;
    }

    /**
     * Get position in series.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
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
        $this->position = $position;
        return $this;
    }

    /**
     * Get replacement number (to disambiguate items in same position).
     *
     * @return int
     */
    public function getReplacementNumber(): int
    {
        return $this->replacementNumber;
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
        $this->replacementNumber = $number;
        return $this;
    }

    /**
     * Get preferred item alternate title (if any).
     *
     * @return ?ItemsAltTitleEntityInterface
     */
    public function getPreferredItemAlternateTitle(): ?ItemsAltTitleEntityInterface
    {
        return $this->preferredItemAltName;
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
        if ($altTitle instanceof ItemsAltTitle || $altTitle === null) {
            $this->preferredItemAltName = $altTitle;
            return $this;
        } elseif ($altTitle instanceof ItemsAltTitleEntityInterface) {
            $altTitle = $altTitle->getId();
        }
        $this->preferredItemAltName = $this->entityManager->getReference(ItemsAltTitle::class, $altTitle);
        return $this;
    }

    /**
     * Get preferred series alternate title (if any).
     *
     * @return ?SeriesAltTitleEntityInterface
     */
    public function getPreferredSeriesAlternateTitle(): ?SeriesAltTitleEntityInterface
    {
        return $this->preferredSeriesAltName;
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
        if ($altTitle instanceof SeriesAltTitle || $altTitle === null) {
            $this->preferredSeriesAltName = $altTitle;
            return $this;
        } elseif ($altTitle instanceof SeriesAltTitleEntityInterface) {
            $altTitle = $altTitle->getId();
        }
        $this->preferredSeriesAltName = $this->entityManager->getReference(SeriesAltTitle::class, $altTitle);
        return $this;
    }

    /**
     * Get the length of the edition.
     *
     * @return ?string
     */
    public function getLength(): ?string
    {
        return $this->length;
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
        $this->length = $length;
        return $this;
    }

    /**
     * Get the ending count of the edition.
     *
     * @return ?string
     */
    public function getEndings(): ?string
    {
        return $this->endings;
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
        $this->endings = $endings;
        return $this;
    }

    /**
     * Get the description of the edition.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
        $this->description = $desc;
        return $this;
    }

    /**
     * Get the link to the preferred publisher (if any).
     *
     * @return ?SeriesPublisherEntityInterface
     */
    public function getPreferredPublisher(): ?SeriesPublisherEntityInterface
    {
        return $this->preferredSeriesPublisher;
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
        if ($publisher instanceof SeriesPublisher || $publisher === null) {
            $this->preferredSeriesPublisher = $publisher;
            return $this;
        } elseif ($publisher instanceof SeriesPublisherEntityInterface) {
            $publisher = $publisher->getId();
        }
        $this->preferredSeriesPublisher = $this->entityManager->getReference(SeriesPublisher::class, $publisher);
        return $this;
    }

    /**
     * Get parent edition (if any).
     *
     * @return ?EditionEntityInterface
     */
    public function getParentEdition(): ?EditionEntityInterface
    {
        return $this->parentEdition;
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
        if ($parent instanceof Edition || $parent === null) {
            $this->parentEdition = $parent;
            return $this;
        } elseif ($parent instanceof EditionEntityInterface) {
            $parent = $parent->getId();
        }
        $this->parentEdition = $this->entityManager->getReference(Edition::class, $parent);
        return $this;
    }

    /**
     * Get position in parent edition.
     *
     * @return ?int
     */
    public function getPositionInParent(): ?int
    {
        return $this->positionInParent;
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
        $this->positionInParent = $position;
        return $this;
    }

    /**
     * Get the extent of this edition inside its parent.
     *
     * @return ?string
     */
    public function getExtentInParent(): ?string
    {
        return $this->extentInParent;
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
        $this->extentInParent = $extent;
        return $this;
    }

    /**
     * Get display order within the edition list in the item.
     *
     * @return int
     */
    public function getItemDisplayOrder(): int
    {
        return $this->itemDisplayOrder;
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
        $this->itemDisplayOrder = $position;
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getEditionName();
    }
}
