<?php

/**
 * Item relationship value entity model.
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
 * Item relationship value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Adaptations')]
#[ORM\Index(name: 'idx_358f6486f783af18', columns: ['Source_Item_ID'])]
#[ORM\Index(name: 'adapted_item_id', columns: ['Adapted_Item_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsAdaptation extends AbstractEntity implements ItemsAdaptationEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Source item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Source_Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $sourceItem;

    /**
     * Adapted item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Adapted_Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $adaptedItem;

    /**
     * Get source item.
     *
     * @return ItemEntityInterface
     */
    public function getSourceItem(): ItemEntityInterface
    {
        return $this->sourceItem;
    }

    /**
     * Set source item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setSourceItem(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->sourceItem = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->sourceItem = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }

    /**
     * Get adapted item.
     *
     * @return ItemEntityInterface
     */
    public function getAdaptedItem(): ItemEntityInterface
    {
        return $this->adaptedItem;
    }

    /**
     * Set adapted item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setAdaptedItem(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->adaptedItem = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->adaptedItem = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }
}
