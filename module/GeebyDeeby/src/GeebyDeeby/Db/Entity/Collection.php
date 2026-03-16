<?php

/**
 * Collection entry entity model.
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
use GeebyDeeby\Db\Entity\Enum\CollectionStatus;

/**
 * Collection entry entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Collections')]
#[ORM\Index(name: 'idx_55b95c397e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'item_id', columns: ['Item_ID'])]
#[ORM\Index(name: 'user_id', columns: ['User_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Collection extends AbstractEntity implements CollectionEntityInterface
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
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $item;

    /**
     * User.
     *
     * @var ?User
     */
    #[ORM\JoinColumn(
        name: 'User_ID',
        referencedColumnName: 'User_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    protected ?User $user;

    /**
     * What type of collection is this?
     *
     * @var CollectionStatus
     */
    #[ORM\Id]
    #[ORM\Column(
        name: 'Collection_Status',
        type: 'enum',
        enumType: CollectionStatus::class,
        nullable: false,
        options: ['default' => CollectionStatus::Have]
    )]
    protected CollectionStatus $status = CollectionStatus::Have;

    /**
     * Collection note.
     *
     * @var ?string
     */
    #[Orm\Column(name: 'Collection_Note', type: 'text', length: 255, nullable: true)]
    protected ?string $note;

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
     * Get associated User (if any).
     *
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    /**
     * Set associated User (if any).
     *
     * @param int|UserEntityInterface $user Associated User entity or ID (null for none)
     *
     * @return static
     */
    public function setUser(int|UserEntityInterface $user): static
    {
        if ($user instanceof User) {
            $this->user = $user;
            return $this;
        } elseif ($user instanceof UserEntityInterface) {
            $user = $user->getId();
        }
        $this->user = $this->entityManager->getReference(User::class, $user);
        return $this;
    }

    /**
     * Get collection status (have, want, or extra).
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status->value;
    }

    /**
     * Set collection status (have, want, or extra).
     *
     * @param string $status New status
     *
     * @return static
     */
    public function setStatus(string $status): static
    {
        $this->status = CollectionStatus::from($status);
        return $this;
    }

    /**
     * Get the note associated with the collection entry.
     *
     * @return ?string
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set the note associated with the collection entry.
     *
     * @param ?string $note New note (null to clear)
     *
     * @return static
     */
    public function setNote(?string $note): static
    {
        $this->note = $note;
        return $this;
    }
}
