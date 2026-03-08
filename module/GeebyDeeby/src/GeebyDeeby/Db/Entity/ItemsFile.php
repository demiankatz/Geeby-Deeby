<?php

/**
 * Item file entity model.
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
 * Item file entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @file     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Files')]
#[ORM\Index(name: 'idx_45b42a3173386fa8', columns: ['Item_ID'])]
#[ORM\Index(name: 'file_id', columns: ['File_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsFile extends AbstractEntity implements ItemsFileEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $item;

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
     * Get associated item.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->item;
    }

    /**
     * Set associated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setItem(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->item = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->item = $this->entityManager->getReference(Item::class, $item);
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
