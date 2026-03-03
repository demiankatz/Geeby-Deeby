<?php

/**
 * User entity model.
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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use GeebyDeeby\Db\Entity\Enum\Approved;

/**
 * User entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Users')]
#[ORM\Index(name: 'person_id', columns: ['Person_ID'])]
#[ORM\Index(name: 'user_group_id', columns: ['User_Group_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class User extends AbstractEntity implements UserEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'User_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Username.
     *
     * @var string
     */
    #[ORM\Column(name: 'Username', type: 'text', length: 255, nullable: false)]
    protected string $username;

    /**
     * Password hash.
     *
     * @var string
     */
    #[ORM\Column(name: 'Password_Hash', type: 'text', length: 255, nullable: false)]
    protected string $passwordHash;

    /**
     * Full name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Name', type: 'text', length: 255, nullable: false)]
    protected string $name;

    /**
     * Email address.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Address', type: 'text', length: 255, nullable: true)]
    protected ?string $address;

    /**
     * Associated person.
     *
     * @var ?Person
     */
    #[ORM\JoinColumn(
        name: 'Person_ID',
        referencedColumnName: 'Person_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    protected ?Person $person = null;

    /**
     * Associated user group.
     *
     * @var ?UserGroup
     */
    #[ORM\JoinColumn(
        name: 'User_Group_ID',
        referencedColumnName: 'User_Group_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: UserGroup::class)]
    protected ?UserGroup $userGroup = null;

    /**
     * Join reason.
     *
     * @var ?string
     */
    #[ORM\Column(name: 'Join_Reason', type: 'text', length: 65535, nullable: true, options: ['default' => null])]
    protected ?string $joinReason = null;

    /**
     * Last login date/time.
     *
     * @var ?DateTime
     */
    #[ORM\Column(name: 'Last_Login', type: 'datetime', nullable: true)]
    protected ?DateTime $lastLogin = null;

    /**
     * Has the user been approved?
     *
     * @var string
     */
    #[ORM\Column(
        name: 'Approved',
        type: 'enum',
        enumType: Approved::class,
        nullable: false,
        options: ['default' => Approved::No]
    )]
    protected string $approved = Approved::No;

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
     * Get username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username Username
     *
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get password hash.
     *
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * Set password hash.
     *
     * @param string $hash Password hash
     *
     * @return static
     */
    public function setPasswordHash(string $hash): static
    {
        $this->passwordHash = $hash;
        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name Name
     *
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Set address.
     *
     * @param string $address Address
     *
     * @return static
     */
    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get associated person (if any).
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): ?PersonEntityInterface
    {
        return $this->person;
    }

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface|null $person Associated person entity or ID (null for none)
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface|null $person): static
    {
        if ($person instanceof Person || $person === null) {
            $this->person = $person;
            return $this;
        } elseif ($person instanceof PersonEntityInterface) {
            $person = $person->getId();
        }
        $this->person = $this->entityManager->getReference(Person::class, $person);
        return $this;
    }

    /**
     * Get associated user group (if any).
     *
     * @return UserGroupEntityInterface
     */
    public function getUserGroup(): ?UserGroupEntityInterface
    {
        return $this->userGroup;
    }

    /**
     * Set associated user group.
     *
     * @param int|UserGroupEntityInterface|null $group Associated user group entity or ID (null for none)
     *
     * @return static
     */
    public function setUserGroup(int|UserGroupEntityInterface|null $group): static
    {
        if ($group instanceof UserGroup || $group === null) {
            $this->userGroup = $group;
            return $this;
        } elseif ($group instanceof UserGroupEntityInterface) {
            $group = $group->getId();
        }
        $this->userGroup = $this->entityManager->getReference(UserGroup::class, $group);
        return $this;
    }

    /**
     * Get join reason.
     *
     * @return string
     */
    public function getJoinReason(): string
    {
        return $this->joinReason;
    }

    /**
     * Set join reason.
     *
     * @param string $reason Join reason
     *
     * @return static
     */
    public function setJoinReason(string $reason): static
    {
        $this->joinReason = $reason;
        return $this;
    }

    /**
     * Get the date and time the user last logged in.
     *
     * @return DateTime
     */
    public function getLastLoginDate(): DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Set the date and time the user last logged in.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setLastLoginDate(string|DateTime $date): static
    {
        $this->lastLogin = $date instanceof DateTime ? $date : DateTime::createFromFormat('Y-m-d h:i:s', $date);
        return $this;
    }

    /**
     * Is the user approved?
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved === 'y';
    }

    /**
     * Set whether the user is approved.
     *
     * @param bool $approved Is the user approved?
     *
     * @return static
     */
    public function setIsApproved(bool $approved): static
    {
        $this->approved = $approved ? 'y' : 'n';
        return $this;
    }
}
