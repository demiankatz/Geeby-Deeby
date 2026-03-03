<?php

/**
 * Person entity model.
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
 * Person entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'People')]
#[ORM\Index(name: 'authority_id', columns: ['Authority_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Person extends AbstractEntity implements PersonEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Person_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * First name.
     *
     * @var string
     */
    #[ORM\Column(name: 'First_Name', type: 'text', length: 255, nullable: true)]
    protected string $firstName;

    /**
     * Last name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Last_Name', type: 'text', length: 255, nullable: true)]
    protected string $lastName;

    /**
     * Extra details.
     *
     * @var string
     */
    #[ORM\Column(name: 'Extra_Details', type: 'text', length: 255, nullable: true)]
    protected string $extraDetails;

    /**
     * Biography.
     *
     * @var string
     */
    #[ORM\Column(name: 'Biography', type: 'text', length: 65535, nullable: true)]
    protected string $biography;

    /**
     * Authority source.
     *
     * @var ?Authority
     */
    #[ORM\JoinColumn(
        name: 'Authority_ID',
        referencedColumnName: 'Authority_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: Authority::class)]
    protected ?Authority $authority = null;

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
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        $n = $this->getFirstName() . ' ' . $this->getLastName() . ' ' . $this->getExtraDetails();
        return trim(preg_replace(['/\s+/', '/\s+,/'], [' ', ','], $n));
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Set first name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setFirstName(string $name): static
    {
        $this->firstName = $name;
        return $this;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * Set last name.
     *
     * @param string $name New value
     *
     * @return static
     */
    public function setLastName(string $name): static
    {
        $this->lastName = $name;
        return $this;
    }

    /**
     * Get extra details.
     *
     * @return string
     */
    public function getExtraDetails(): string
    {
        return $this->extraDetails;
    }

    /**
     * Set extra details.
     *
     * @param string $details New value
     *
     * @return static
     */
    public function setExtraDetails(string $details): static
    {
        $this->extraDetails = $details;
        return $this;
    }

    /**
     * Get biography.
     *
     * @return string
     */
    public function getBiography(): string
    {
        return $this->biography;
    }

    /**
     * Set biography.
     *
     * @param string $bio New value
     *
     * @return static
     */
    public function setBiography(string $bio): static
    {
        $this->biography = $bio;
        return $this;
    }

    /**
     * Get associated authority (if any).
     *
     * @return ?AuthorityEntityInterface
     */
    public function getAuthority(): ?AuthorityEntityInterface
    {
        return $this->authority;
    }

    /**
     * Set associated authority.
     *
     * @param null|int|AuthorityEntityInterface $authority Associated authority entity or ID, or null
     *
     * @return static
     */
    public function setAuthority(null|int|AuthorityEntityInterface $authority): static
    {
        if ($authority instanceof Authority || $authority === null) {
            $this->authority = $authority;
            return $this;
        } elseif ($authority instanceof AuthorityEntityInterface) {
            $authority = $authority->getId();
        }
        $this->authority = $this->entityManager->getReference(Authority::class, $authority);
        return $this;
    }
}
