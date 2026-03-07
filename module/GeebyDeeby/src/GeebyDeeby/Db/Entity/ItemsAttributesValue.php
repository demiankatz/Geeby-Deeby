<?php

/**
 * Item attribute value entity model.
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
 * Item attribute value entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Attributes_Values')]
#[ORM\Index(name: 'idx_9380093973386fa8', columns: ['Item_ID'])]
#[ORM\Index(name: 'items_attribute_id', columns: ['Items_Attribute_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsAttributesValue extends AbstractEntity implements ItemsAttributesValueEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Item.
     *
     * @var Item
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Item_ID',
        referencedColumnName: 'Item_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $item;

    /**
     * Item attribute.
     *
     * @var ItemsAttribute
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Items_Attribute_ID',
        referencedColumnName: 'Items_Attribute_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: ItemsAttribute::class)]
    protected ItemsAttribute $attribute;

    /**
     * Attribute value.
     *
     * @var string
     */
    #[ORM\Column(name: 'Items_Attribute_Value', type: 'text', length: 16777215, nullable: false)]
    protected string $value;

    /**
     * Get associated item.
     *
     * @return ?ItemEntityInterface
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
    public function setItem(int|ItemEntityInterface|null $item): static
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
     * Get associated attribute.
     *
     * @return ?ItemsAttributeEntityInterface
     */
    public function getAttribute(): ItemsAttributeEntityInterface
    {
        return $this->attribute;
    }

    /**
     * Set associated attribute.
     *
     * @param int|ItemsAttributeEntityInterface $attribute Associated item entity or ID
     *
     * @return static
     */
    public function setAttribute(int|ItemsAttributeEntityInterface|null $attribute): static
    {
        if ($attribute instanceof ItemsAttribute) {
            $this->attribute = $attribute;
            return $this;
        } elseif ($attribute instanceof ItemsAttributeEntityInterface) {
            $attribute = $attribute->getId();
        }
        $this->attribute = $this->entityManager->getReference(ItemsAttribute::class, $attribute);
        return $this;
    }

    /**
     * Get the value of the attribute.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the value of the attribute.
     *
     * @param string $value New value
     *
     * @return static
     */
    public function setValue(string $value): static
    {
        $this->value = $value;
        return $this;
    }
}
