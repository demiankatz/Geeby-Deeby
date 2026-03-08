<?php

/**
 * Row Definition for Files
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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

use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\FileTypeEntityInterface;

/**
 * Row Definition for Files
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class File extends TableAwareGateway implements FileEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('File_ID', 'Files', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->File_ID ?? null;
    }

    /**
     * Get the name of the file.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->File_Name;
    }

    /**
     * Set the name of the file.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setFileName(string $name): static
    {
        $this->File_Name = $name;
        return $this;
    }

    /**
     * Get the path of the file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->File_Path;
    }

    /**
     * Set the path of the file.
     *
     * @param string $path New path.
     *
     * @return static
     */
    public function setFilePath(string $path): static
    {
        $this->File_Path = $path;
        return $this;
    }

    /**
     * Get a description of the file.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->Description;
    }

    /**
     * Set the description of the file.
     *
     * @param string $description New description
     *
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->Description = $description;
        return $this;
    }

    /**
     * Get associated file type.
     *
     * @return FileTypeEntityInterface
     */
    public function getFileType(): FileTypeEntityInterface
    {
        return $this->getTableManager()->get('filetype')->getByPrimaryKey($this->File_Type_ID);
    }

    /**
     * Set associated file type.
     *
     * @param int|FileTypeEntityInterface $fileType Associated language entity or ID
     *
     * @return static
     */
    public function setFileType(int|FileTypeEntityInterface $fileType): static
    {
        $this->File_Type_ID = $fileType instanceof FileTypeEntityInterface
            ? $fileType->getId() : $fileType;
        return $this;
    }
}
