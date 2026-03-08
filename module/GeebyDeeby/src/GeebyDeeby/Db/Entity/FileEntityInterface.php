<?php

/**
 * Interface for file entity models.
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
 * Interface for file entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface FileEntityInterface extends EntityInterface
{
    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int;

    /**
     * Get the name of the file.
     *
     * @return string
     */
    public function getFileName(): string;

    /**
     * Set the name of the file.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setFileName(string $name): static;

    /**
     * Get the path of the file.
     *
     * @return string
     */
    public function getFilePath(): string;

    /**
     * Set the path of the file.
     *
     * @param string $path New path.
     *
     * @return static
     */
    public function setFilePath(string $path): static;

    /**
     * Get a description of the file.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Set the description of the file.
     *
     * @param string $description New description
     *
     * @return static
     */
    public function setDescription(string $description): static;

    /**
     * Get associated file type.
     *
     * @return FileTypeEntityInterface
     */
    public function getFileType(): FileTypeEntityInterface;

    /**
     * Set associated file type.
     *
     * @param int|FileTypeEntityInterface $fileType Associated language entity or ID
     *
     * @return static
     */
    public function setFileType(int|FileTypeEntityInterface $fileType): static;
}
