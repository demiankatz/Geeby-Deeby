<?php

/**
 * Interface for item collection entry entity models.
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

/**
 * Interface for item collection entry entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface ItemsInCollectionEntityInterface extends EntityInterface
{
    /**
     * Get collected item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface;

    /**
     * Set collected item.
     *
     * @param int|ItemEntityInterface $item Collected item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static;

    /**
     * Get collection (container) item.
     *
     * @return ItemEntityInterface
     */
    public function getCollectionItem(): ItemEntityInterface;

    /**
     * Set collection (container) item.
     *
     * @param int|ItemEntityInterface $item Collection (container) item entity or ID
     *
     * @return static
     */
    public function setCollectionItem(int|ItemEntityInterface $item): static;

    /**
     * Get position in credits.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Set position in credits.
     *
     * @param int $position Position in credits
     *
     * @return static
     */
    public function setPosition(int $position): static;

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
