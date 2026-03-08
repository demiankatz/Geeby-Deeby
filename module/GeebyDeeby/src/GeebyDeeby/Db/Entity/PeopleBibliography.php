<?php

/**
 * Person bibliography entity model.
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
 * Person bibliography entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'People_Bibliography')]
#[ORM\Index(name: 'idx_239673c0d603d8e', columns: ['Person_ID'])]
#[ORM\Index(name: 'item_id', columns: ['Item_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class PeopleBibliography extends AbstractEntity implements PeopleBibliographyEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Source person.
     *
     * @var Person
     */
    #[ORM\JoinColumn(
        name: 'Person_ID',
        referencedColumnName: 'Person_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected Person $person;

    /**
     * Bibliography item.
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
     * Get the subject person.
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): PersonEntityInterface
    {
        return $this->person;
    }

    /**
     * Set the subject person.
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
     * Get item about the subject person.
     *
     * @return ItemEntityInterface
     */
    public function getItem(): ItemEntityInterface
    {
        return $this->item;
    }

    /**
     * Set item about the subject person.
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
}
