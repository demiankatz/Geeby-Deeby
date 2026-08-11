<?php

/**
 * Item description entity model.
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
use GeebyDeeby\Db\Entity\Enum\DescriptionSource;

/**
 * Item description entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Descriptions')]
#[ORM\Index(name: 'idx_dd7feef973386fa8', columns: ['Item_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsDescription extends AbstractEntity implements ItemsDescriptionEntityInterface
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
     * What type of description is this?
     *
     * @var DescriptionSource
     */
    #[ORM\Id]
    #[ORM\Column(
        name: 'Source',
        type: 'enum',
        enumType: DescriptionSource::class,
        nullable: false,
        options: ['default' => DescriptionSource::User]
    )]
    protected DescriptionSource $source = DescriptionSource::User;

    /**
     * Description.
     *
     * @var ?string
     */
    #[Orm\Column(name: 'Description', type: 'text', length: 65535, nullable: false)]
    protected ?string $description;

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
     * Get the description source.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->source->value;
    }

    /**
     * Set the description source.
     *
     * @param string $source New source.
     *
     * @return static
     */
    public function setSource(string $source): static
    {
        $this->source = DescriptionSource::from($source);
        return $this;
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description.
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
}
