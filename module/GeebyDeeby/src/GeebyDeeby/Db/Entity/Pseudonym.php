<?php

/**
 * Pseudonym entity model.
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
 * Pseudonym entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Pseudonyms')]
#[ORM\Index(name: 'idx_a46a0b2e4e4dcfc7', columns: ['Real_Person_ID'])]
#[ORM\Index(name: 'pseudo_person_id', columns: ['Pseudo_Person_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Pseudonym extends AbstractEntity implements PseudonymEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Real person.
     *
     * @var Person
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Real_Person_ID',
        referencedColumnName: 'Person_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected Person $realPerson;

    /**
     * Pseudonym "person".
     *
     * @var Person
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Pseudo_Person_ID',
        referencedColumnName: 'Person_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected Person $pseudoPerson;

    /**
     * Get associated real person.
     *
     * @return PersonEntityInterface
     */
    public function getRealPerson(): PersonEntityInterface
    {
        return $this->realPerson;
    }

    /**
     * Set associated real person.
     *
     * @param int|PersonEntityInterface $person Associated person entity or ID
     *
     * @return static
     */
    public function setRealPerson(int|PersonEntityInterface $person): static
    {
        if ($person instanceof Person) {
            $this->realPerson = $person;
            return $this;
        } elseif ($person instanceof PersonEntityInterface) {
            $person = $person->getId();
        }
        $this->realPerson = $this->entityManager->getReference(Person::class, $person);
        return $this;
    }

    /**
     * Get associated pseudo-person.
     *
     * @return PersonEntityInterface
     */
    public function getPseudoPerson(): PersonEntityInterface
    {
        return $this->pseudoPerson;
    }

    /**
     * Set associated pseudo-person.
     *
     * @param int|PersonEntityInterface $person Associated person entity or ID
     *
     * @return static
     */
    public function setPseudoPerson(int|PersonEntityInterface $person): static
    {
        if ($person instanceof Person) {
            $this->pseudoPerson = $person;
            return $this;
        } elseif ($person instanceof PersonEntityInterface) {
            $person = $person->getId();
        }
        $this->pseudoPerson = $this->entityManager->getReference(Person::class, $person);
        return $this;
    }
}
