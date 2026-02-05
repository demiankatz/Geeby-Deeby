<?php

/**
 * Interface for edition image entity models.
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
 * Interface for edition image entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface EditionsImageEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface;

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static;

    /**
     * Get image path.
     *
     * @return ?string
     */
    public function getImagePath(): ?string;

    /**
     * Set image path.
     *
     * @param ?string $path New path
     *
     * @return static
     */
    public function setImagePath(?string $path): static;

    /**
     * Get thumb path.
     *
     * @return ?string
     */
    public function getThumbPath(): ?string;

    /**
     * Set thumb path.
     *
     * @param ?string $path New path
     *
     * @return static
     */
    public function setThumbPath(?string $path): static;

    /**
     * Get IIIF URI.
     *
     * @return ?string
     */
    public function getIiifUri(): ?string;

    /**
     * Set IIIF URI.
     *
     * @param ?string $uri New URI
     *
     * @return static
     */
    public function setIiifUri(?string $uri): static;

    /**
     * Get position in credits.
     *
     * @return ?int
     */
    public function getPosition(): ?int;

    /**
     * Set position in credits.
     *
     * @param ?int $position Position in credits
     *
     * @return static
     */
    public function setPosition(?int $position): static;

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface;

    /**
     * Set associated note (if any).
     *
     * @param int|NoteEntityInterface|null $note Associated note entity or ID (null for none)
     *
     * @return static
     */
    public function setNote(int|NoteEntityInterface|null $note): static;
}
