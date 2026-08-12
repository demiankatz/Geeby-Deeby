<?php

/**
 * Edition ISBN entity model.
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
 * Edition ISBN entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_ISBNs')]
#[ORM\Index(name: 'edition_id', columns: ['Edition_ID'])]
#[ORM\Index(name: 'note_id', columns: ['Note_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsIsbn extends AbstractEntity implements EditionsIsbnEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Sequence_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Edition.
     *
     * @var Edition
     */
    #[ORM\JoinColumn(
        name: 'Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * ISBN-10.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'ISBN', type: 'string', length: 10, nullable: true, options: ['fixed' => true])]
    protected ?string $isbn10;

    /**
     * ISBN-13.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'ISBN13', type: 'string', length: 13, nullable: true, options: ['fixed' => true])]
    protected ?string $isbn13;

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
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

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
     * Get the value of the ISBN-10.
     *
     * @return ?string
     */
    public function getIsbn10(): ?string
    {
        return $this->isbn10;
    }

    /**
     * Set the value of the ISBN-10.
     *
     * @param ?string $isbn New ISBN-10
     *
     * @return static
     */
    public function setIsbn10(?string $isbn): static
    {
        $this->isbn10 = $isbn;
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

    /**
     * Get the value of the ISBN-13.
     *
     * @return ?string
     */
    public function getIsbn13(): ?string
    {
        return $this->isbn13;
    }

    /**
     * Set the value of the ISBN-13.
     *
     * @param ?string $isbn New ISBN-13
     *
     * @return static
     */
    public function setIsbn13(?string $isbn): static
    {
        $this->isbn13 = $isbn;
        return $this;
    }
}
