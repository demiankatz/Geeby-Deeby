<?php

/**
 * Person link entity model.
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
 * Person link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'People_Links')]
#[ORM\Index(name: 'idx_c3112fa4d603d8e', columns: ['Person_ID'])]
#[ORM\Index(name: 'link_id', columns: ['Link_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class PeopleLink extends AbstractEntity implements PeopleLinkEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Person.
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
     * Link.
     *
     * @var Link
     */
    #[ORM\JoinColumn(
        name: 'Link_ID',
        referencedColumnName: 'Link_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Link::class)]
    protected Link $link;

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
     * Get associated link.
     *
     * @return LinkEntityInterface
     */
    public function getLink(): LinkEntityInterface
    {
        return $this->link;
    }

    /**
     * Set associated link.
     *
     * @param int|LinkEntityInterface $link Associated link entity or ID
     *
     * @return static
     */
    public function setLink(int|LinkEntityInterface $link): static
    {
        if ($link instanceof Link) {
            $this->link = $link;
            return $this;
        } elseif ($link instanceof LinkEntityInterface) {
            $link = $link->getId();
        }
        $this->link = $this->entityManager->getReference(Link::class, $link);
        return $this;
    }
}
