<?php

/**
 * Item/creator link entity model.
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
 * Item/creator link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Creators')]
#[ORM\UniqueConstraint(name: 'Item_ID', columns: ['Item_ID', 'Person_ID', 'Role_ID'])]
#[ORM\Index(name: 'idx_8c60534373386fa8', columns: ['Item_ID'])]
#[ORM\Index(name: 'person_id', columns: ['Person_ID'])]
#[ORM\Index(name: 'role_id', columns: ['Role_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsCreator extends AbstractEntity implements ItemsCreatorEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Item_Creator_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Item.
     *
     * @var Item
     */
    #[ORM\JoinColumn(name: 'Item_ID', referencedColumnName: 'Item_ID', nullable: false, options: ['default' => 0])]
    #[ORM\ManyToOne(targetEntity: Item::class)]
    protected Item $item;

    /**
     * Person.
     *
     * @var Person
     */
    #[ORM\JoinColumn(name: 'Person_ID', referencedColumnName: 'Person_ID', nullable: false, options: ['default' => 0])]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected Person $person;

    /**
     * Role.
     *
     * @var Role
     */
    #[ORM\JoinColumn(name: 'Role_ID', referencedColumnName: 'Role_ID', nullable: false, options: ['default' => 0])]
    #[ORM\ManyToOne(targetEntity: Role::class)]
    protected Role $role;

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
     * Get associated person.
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): PersonEntityInterface
    {
        return $this->person;
    }

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface $person Associated person entity or ID
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface $person): static
    {
        if ($person instanceof Person) {
            $this->person = $person;
            return $this;
        } elseif ($person instanceof PersonEntityInterface) {
            $person = $person->getId();
        }
        $this->person = $this->entityManager->getReference(Person::class, $person);
        return $this;
    }

    /**
     * Get associated role.
     *
     * @return RoleEntityInterface
     */
    public function getRole(): RoleEntityInterface
    {
        return $this->role;
    }

    /**
     * Set associated role.
     *
     * @param int|RoleEntityInterface $role Associated role entity or ID
     *
     * @return static
     */
    public function setRole(int|RoleEntityInterface $role): static
    {
        if ($role instanceof Role) {
            $this->role = $role;
            return $this;
        } elseif ($role instanceof RoleEntityInterface) {
            $role = $role->getId();
        }
        $this->role = $this->entityManager->getReference(Role::class, $role);
        return $this;
    }
}
