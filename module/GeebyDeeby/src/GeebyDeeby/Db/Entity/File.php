<?php

/**
 * File entity model.
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
 * File entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Files')]
#[ORM\Index(name: 'file_type_id', columns: ['File_Type_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class File extends AbstractEntity implements FileEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'File_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * File name.
     *
     * @var string
     */
    #[ORM\Column(name: 'File_Name', type: 'text', length: 255, nullable: true)]
    protected string $fileName;

    /**
     * File path.
     *
     * @var string
     */
    #[ORM\Column(name: 'File_Path', type: 'text', length: 255, nullable: true)]
    protected string $path;

    /**
     * Description.
     *
     * @var string
     */
    #[ORM\Column(name: 'Description', type: 'text', length: 65535, nullable: true)]
    protected string $description;

    /**
     * File type.
     *
     * @var FileType
     */
    #[ORM\JoinColumn(
        name: 'File_Type_ID',
        referencedColumnName: 'File_Type_ID',
        nullable: true
    )]
    #[ORM\ManyToOne(targetEntity: FileType::class)]
    protected FileType $fileType;

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
     * Get the name of the file.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
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
        $this->fileName = $name;
        return $this;
    }

    /**
     * Get the path of the file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->path;
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
        $this->path = $path;
        return $this;
    }

    /**
     * Get a description of the file.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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
        $this->description = $description;
        return $this;
    }

    /**
     * Get associated file type.
     *
     * @return FileTypeEntityInterface
     */
    public function getFileType(): FileTypeEntityInterface
    {
        return $this->fileType;
    }

    /**
     * Set associated file type.
     *
     * @param int|FileTypeEntityInterface $fileType Associated fileType entity or ID
     *
     * @return static
     */
    public function setFileType(int|FileTypeEntityInterface $fileType): static
    {
        if ($fileType instanceof FileType) {
            $this->fileType = $fileType;
            return $this;
        } elseif ($fileType instanceof FileTypeEntityInterface) {
            $fileType = $fileType->getId();
        }
        $this->fileType = $this->entityManager->getReference(FileType::class, $fileType);
        return $this;
    }
}
