<?php

/**
 * Item entity model.
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
 * Item entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items')]
#[ORM\Index(name: 'material_type_id', columns: ['Material_Type_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Item extends AbstractEntity implements ItemEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Item_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Item name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Item_Name', type: 'text', length: 65535, nullable: false)]
    protected string $itemName;

    /**
     * Item errata.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Item_Errata', type: 'text', length: 65535, nullable: true)]
    protected ?string $errata;

    /**
     * Item thanks.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Item_Thanks', type: 'text', length: 255, nullable: true)]
    protected ?string $thanks;

    /**
     * Material type.
     *
     * @var MaterialType
     */
    #[ORM\JoinColumn(
        name: 'Material_Type_ID',
        referencedColumnName: 'Material_Type_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: MaterialType::class)]
    protected MaterialType $materialType;

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
     * Get the name of the item.
     *
     * @return string
     */
    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * Set the name of the item.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setItemName(string $name): static
    {
        $this->itemName = $name;
        return $this;
    }

    /**
     * Get errata for the item.
     *
     * @return ?string
     */
    public function getErrata(): ?string
    {
        return $this->errata;
    }

    /**
     * Set errata for the item.
     *
     * @param ?string $errata New errata
     *
     * @return static
     */
    public function setErrata(?string $errata): static
    {
        $this->errata = $errata;
        return $this;
    }

    /**
     * Get thanks for the item.
     *
     * @return ?string
     */
    public function getThanks(): ?string
    {
        return $this->thanks;
    }

    /**
     * Set thanks for the item.
     *
     * @param ?string $thanks New thanks
     *
     * @return static
     */
    public function setThanks(?string $thanks): static
    {
        $this->thanks = $thanks;
        return $this;
    }

    /**
     * Get associated material type.
     *
     * @return MaterialTypeEntityInterface
     */
    public function getMaterialType(): MaterialTypeEntityInterface
    {
        return $this->materialType;
    }

    /**
     * Set associated material type.
     *
     * @param int|MaterialTypeEntityInterface $materialType Associated materialType entity or ID
     *
     * @return static
     */
    public function setMaterialType(int|MaterialTypeEntityInterface $materialType): static
    {
        if ($materialType instanceof MaterialType) {
            $this->materialType = $materialType;
            return $this;
        } elseif ($materialType instanceof MaterialTypeEntityInterface) {
            $materialType = $materialType->getId();
        }
        $this->materialType = $this->entityManager->getReference(MaterialType::class, $materialType);
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getItemName();
    }
}
