<?php

/**
 * Interface for edition full text entity models.
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
 * Interface for edition full text entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface EditionsFullTextEntityInterface extends EntityInterface
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
     * Get associated full text source id.
     *
     * @return FullTextSourceEntityInterface
     */
    public function getFullTextSource(): FullTextSourceEntityInterface;

    /**
     * Set associated full text source id.
     *
     * @param int|FullTextSourceEntityInterface $fts Associated full text source entity or ID
     *
     * @return static
     */
    public function setFullTextSource(int|FullTextSourceEntityInterface $fts): static;

    /**
     * Get the URL of the full text resource.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set the URL of the full text resource.
     *
     * @param string $url New url.
     *
     * @return static
     */
    public function setUrl(string $url): static;
}
