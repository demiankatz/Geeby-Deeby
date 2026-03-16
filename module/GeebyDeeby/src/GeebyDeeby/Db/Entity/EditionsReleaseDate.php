<?php

/**
 * Edition release date entity model.
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
 * Edition release date entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Release_Dates')]
#[ORM\Index(name: 'idx_9ed321a6959f1de4', columns: ['Edition_ID'])]
#[ORM\Index(name: 'note_id', columns: ['Note_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsReleaseDate extends AbstractEntity implements EditionsReleaseDateEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Edition.
     *
     * @var Edition
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * Month.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'Month', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $month;

    /**
     * Day.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'Day', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $day;

    /**
     * Year.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'Year', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $year;

    /**
     * Note.
     *
     * @var ?Note
     */
    #[ORM\JoinColumn(
        name: 'Note_ID',
        referencedColumnName: 'Note_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Note::class)]
    protected ?Note $note;

    /**
     * Get associated edition.
     *
     * @return ?EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->edition;
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface|null $edition): static
    {
        if ($edition instanceof Edition) {
            $this->edition = $edition;
            return $this;
        } elseif ($edition instanceof EditionEntityInterface) {
            $edition = $edition->getId();
        }
        $this->edition = $this->entityManager->getReference(Edition::class, $edition);
        return $this;
    }

    /**
     * Get year (or -1 for unpublished).
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Set year.
     *
     * @param int $year Year (or -1 for unpublished)
     *
     * @return static
     */
    public function setYear(int $year): static
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Get month (or 0 for unspecified).
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Set month.
     *
     * @param int $month Month (or 0 for unspecified)
     *
     * @return static
     */
    public function setMonth(int $month): static
    {
        $this->month = $month;
        return $this;
    }

    /**
     * Get day (or 0 for unspecified).
     *
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Set day.
     *
     * @param int $day Day (or 0 for unspecified)
     *
     * @return static
     */
    public function setDay(int $day): static
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface
    {
        return $this->note;
    }

    /**
     * Set associated note (if any).
     *
     * @param int|NoteEntityInterface|null $note Associated note entity or ID (null for none)
     *
     * @return static
     */
    public function setNote(int|NoteEntityInterface|null $note): static
    {
        if ($note instanceof Note || null === $note) {
            $this->note = $note;
            return $this;
        } elseif ($note instanceof NoteEntityInterface) {
            $note = $note->getId();
        }
        $this->note = $this->entityManager->getReference(Note::class, $note);
        return $this;
    }
}
