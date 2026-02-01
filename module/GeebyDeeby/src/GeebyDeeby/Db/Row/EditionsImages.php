<?php

/**
 * Row Definition for Editions_Images
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsImageEntityInterface;
use GeebyDeeby\Db\Entity\NoteEntityInterface;

/**
 * Row Definition for Editions_Images
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsImages extends TableAwareGateway implements EditionsImageEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'Editions_Images', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Sequence_ID ?? null;
    }

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->getTableManager()->get('edition')->getByPrimaryKey($this->Edition_ID);
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static
    {
        $this->Edition_ID = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        return $this;
    }

    /**
     * Get image path.
     *
     * @return ?string
     */
    public function getImagePath(): ?string
    {
        return $this->Image_Path;
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
        $this->Image_Path = $path;
        return $this;
    }

    /**
     * Get thumb path.
     *
     * @return ?string
     */
    public function getThumbPath(): ?string
    {
        return $this->Thumb_Path;
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
        $this->Thumb_Path = $path;
        return $this;
    }

    /**
     * Get IIIF URI.
     *
     * @return ?string
     */
    public function getIiifUri(): ?string
    {
        return $this->IIIF_URI;
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
        $this->IIIF_URI = $uri;
        return $this;
    }

    /**
     * Get position in credits.
     *
     * @return ?int
     */
    public function getPosition(): ?int
    {
        return $this->Position;
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
        $this->Position = $position;
        return $this;
    }

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface
    {
        return $this->Note_ID
            ? $this->getTableManager()->get('note')->getByPrimaryKey($this->Note_ID)
            : null;
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
        $this->Note_ID = $note instanceof NoteEntityInterface ? $note->getId() : $note;
        return $this;
    }
}
