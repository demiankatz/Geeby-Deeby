<?php

/**
 * Edition image entity model.
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
 * Edition image entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Images')]
#[ORM\Index(name: 'edition_id', columns: ['Edition_ID'])]
#[ORM\Index(name: 'note_id', columns: ['Note_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsImage extends AbstractEntity implements EditionsImageEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Sequence_ID', type: 'bigint', nullable: false, options: ['unsigned' => true])]
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
     * Image path.
     *
     * @var string
     */
    #[ORM\Column(name: 'Image_Path', type: 'text', length: 255, nullable: true)]
    protected string $imagePath;

    /**
     * Thumb path.
     *
     * @var string
     */
    #[ORM\Column(name: 'Thumb_Path', type: 'text', length: 255, nullable: true)]
    protected string $thumbPath;

    /**
     * IIIF URI.
     *
     * @var string
     */
    #[ORM\Column(name: 'IIIF_URI', type: 'text', length: 255, nullable: true)]
    protected string $iiifUri;

    /**
     * Position.
     *
     * @var ?int
     */
    #[ORM\Column(name: 'Position', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $position = null;

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
     * Get image path.
     *
     * @return ?string
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * Set image path.
     *
     * @param ?string $path New path
     *
     * @return static
     */
    public function setImagePath(?string $path): static
    {
        $this->imagePath = $path;
        return $this;
    }

    /**
     * Get thumb path.
     *
     * @return ?string
     */
    public function getThumbPath(): ?string
    {
        return $this->thumbPath;
    }

    /**
     * Set thumb path.
     *
     * @param ?string $path New path
     *
     * @return static
     */
    public function setThumbPath(?string $path): static
    {
        $this->thumbPath = $path;
        return $this;
    }

    /**
     * Get IIIF URI.
     *
     * @return ?string
     */
    public function getIiifUri(): ?string
    {
        return $this->iiifUri;
    }

    /**
     * Set IIIF URI.
     *
     * @param ?string $uri New URI
     *
     * @return static
     */
    public function setIiifUri(?string $uri): static
    {
        $this->iiifUri = $uri;
        return $this;
    }

    /**
     * Get position in credits.
     *
     * @return ?int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set position in credits.
     *
     * @param ?int $position Position in credits
     *
     * @return static
     */
    public function setPosition(?int $position): static
    {
        $this->position = $position;
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
