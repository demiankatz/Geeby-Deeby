<?php

/**
 * Series file entity model.
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
 * @file     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Series file entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @file     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Files')]
#[ORM\Index(name: 'idx_aa4a50b87e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'file_id', columns: ['File_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesFile extends AbstractEntity implements SeriesFileEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $series;

    /**
     * File.
     *
     * @var File
     */
    #[ORM\JoinColumn(
        name: 'File_ID',
        referencedColumnName: 'File_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: File::class)]
    protected File $file;

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->series;
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static
    {
        if ($series instanceof Series) {
            $this->series = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->series = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get associated file.
     *
     * @return FileEntityInterface
     */
    public function getFile(): FileEntityInterface
    {
        return $this->file;
    }

    /**
     * Set associated file.
     *
     * @param int|FileEntityInterface $file Associated file entity or ID
     *
     * @return static
     */
    public function setFile(int|FileEntityInterface $file): static
    {
        if ($file instanceof File) {
            $this->file = $file;
            return $this;
        } elseif ($file instanceof FileEntityInterface) {
            $file = $file->getId();
        }
        $this->file = $this->entityManager->getReference(File::class, $file);
        return $this;
    }
}
