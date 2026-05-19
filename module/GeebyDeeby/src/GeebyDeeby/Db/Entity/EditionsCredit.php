<?php

/**
 * Edition/credit link entity model.
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
 * Edition/credit link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Credits')]
#[ORM\Index(name: 'idx_c70f0f00959f1de4', columns: ['Edition_ID'])]
#[ORM\Index(name: 'person_id', columns: ['Person_ID'])]
#[ORM\Index(name: 'role_id', columns: ['Role_ID'])]
#[ORM\Index(name: 'note_id', columns: ['Note_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsCredit extends AbstractEntity implements EditionsCreditEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Edition.
     *
     * @var Edition
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * Person.
     *
     * @var Person
     */
    #[ORM\Id]
    #[ORM\JoinColumn(name: 'Person_ID', referencedColumnName: 'Person_ID', nullable: false, options: ['default' => 0])]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected Person $person;

    /**
     * Role.
     *
     * @var Role
     */
    #[ORM\Id]
    #[ORM\JoinColumn(name: 'Role_ID', referencedColumnName: 'Role_ID', nullable: false, options: ['default' => 0])]
    #[ORM\ManyToOne(targetEntity: Role::class)]
    protected Role $role;

    /**
     * Note.
     *
     * @var ?Note
     */
    #[ORM\JoinColumn(
        name: 'Note_ID',
        referencedColumnName: 'Note_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Note::class)]
    protected ?Note $note;

    /**
     * Position.
     *
     * @var ?int
     */
    #[ORM\Column(name: 'Position', type: 'integer', nullable: true)]
    protected ?int $position;

    /**
     * Map of database field => setter for use by populateFromArray().
     *
     * @var array
     */
    protected array $setterMap = [
        'Edition_ID' => 'setEdition',
        'Note_ID' => 'setNote',
        'Person_ID' => 'setPerson',
        'Position' => 'setPosition',
        'Role_ID' => 'setRole',
    ];

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->edition;
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static
    {
        if ($edition instanceof Edition) {
            $this->edition = $edition;
            return $this;
        } elseif ($edition instanceof EditionEntityInterface) {
            $edition = $edition->getId();
        }
        $this->edition = $this->entityManager->getReference(Edition::class, $edition);
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

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface
    {
        return $this->note;
    }

    /**
     * Set associated note (if any).
     *
     * @param int|NoteEntityInterface|null $note Associated note entity or ID (null for none)
     *
     * @return static
     */
    public function setNote(int|NoteEntityInterface|null $note): static
    {
        if ($note instanceof Note || null === $note) {
            $this->note = $note;
            return $this;
        } elseif ($note instanceof NoteEntityInterface) {
            $note = $note->getId();
        }
        $this->note = $this->entityManager->getReference(Note::class, $note);
        return $this;
    }

    /**
     * Get position in credits.
     *
     * @return ?int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set position in credits.
     *
     * @param ?int $position Position in credits
     *
     * @return static
     */
    public function setPosition(?int $position): static
    {
        $this->position = $position;
        return $this;
    }
}
