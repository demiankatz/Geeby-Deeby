<?php

/**
 * Row Definition for Editions_ISBNs
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
use GeebyDeeby\Db\Entity\EditionsIsbnEntityInterface;
use GeebyDeeby\Db\Entity\NoteEntityInterface;

/**
 * Row Definition for Editions_ISBNs
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsISBNs extends TableAwareGateway implements EditionsIsbnEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'Editions_ISBNs', $adapter);
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
     * Get the value of the ISBN-10.
     *
     * @return ?string
     */
    public function getIsbn10(): ?string
    {
        return $this->ISBN;
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
        $this->ISBN = $isbn;
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

    /**
     * Get the value of the ISBN-13.
     *
     * @return ?string
     */
    public function getIsbn13(): ?string
    {
        return $this->ISBN13;
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
        $this->ISBN13 = $isbn;
        return $this;
    }
}
