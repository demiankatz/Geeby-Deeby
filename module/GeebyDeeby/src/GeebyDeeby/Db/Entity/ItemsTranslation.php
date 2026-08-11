<?php

/**
 * Item translation entity model.
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
 * Item translation entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Translations')]
#[ORM\Index(name: 'idx_d2a69ac8f783af18', columns: ['Source_Item_ID'])]
#[ORM\Index(name: 'trans_item_id', columns: ['Trans_Item_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsTranslation extends AbstractEntity implements ItemsTranslationEntityInterface
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
     * Translated item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(
        name: 'Trans_Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $translatedItem;

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
     * Get translated item.
     *
     * @return ItemEntityInterface
     */
    public function getTranslatedItem(): ItemEntityInterface
    {
        return $this->translatedItem;
    }

    /**
     * Set translated item.
     *
     * @param int|ItemEntityInterface $item Associated item entity or ID
     *
     * @return static
     */
    public function setTranslatedItem(int|ItemEntityInterface $item): static
    {
        if ($item instanceof Item) {
            $this->translatedItem = $item;
            return $this;
        } elseif ($item instanceof ItemEntityInterface) {
            $item = $item->getId();
        }
        $this->translatedItem = $this->entityManager->getReference(Item::class, $item);
        return $this;
    }
}
