<?php

/**
 * Series review entity model.
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
 * Series review entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Reviews')]
#[ORM\Index(name: 'idx_33a295b97e63b755', columns: ['Series_ID'])]
#[ORM\Index(name: 'user_id', columns: ['User_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesReview extends AbstractEntity implements SeriesReviewEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Series.
     *
     * @var Series
     */
    #[ORM\JoinColumn(
        name: 'Series_ID',
        referencedColumnName: 'Series_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $series;

    /**
     * User.
     *
     * @var User
     */
    #[ORM\JoinColumn(
        name: 'User_ID',
        referencedColumnName: 'User_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    protected User $user;

    /**
     * Review text.
     *
     * @var string
     */
    #[ORM\Column(name: 'Review', type: 'text', length: 65535, nullable: false)]
    protected string $review;

    /**
     * Has the review been approved?
     *
     * @var Approved
     */
    #[ORM\Column(
        name: 'Approved',
        type: 'enum',
        enumType: Approved::class,
        nullable: false,
        options: ['default' => Approved::Yes]
    )]
    protected Approved $approved = Approved::Yes;

    /**
     * Date added.
     *
     * @var ?DateTime
     */
    #[ORM\Column(name: 'Added', type: 'date', nullable: false, options: ['default' => '2004-09-23'])]
    protected ?DateTime $added;

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->series;
    }

    /**
     * Set associated series.
     *
     * @param int|SeriesEntityInterface $series Associated series entity or ID
     *
     * @return static
     */
    public function setSeries(int|SeriesEntityInterface $series): static
    {
        if ($series instanceof Series) {
            $this->series = $series;
            return $this;
        } elseif ($series instanceof SeriesEntityInterface) {
            $series = $series->getId();
        }
        $this->series = $this->entityManager->getReference(Series::class, $series);
        return $this;
    }

    /**
     * Get associated user.
     *
     * @return UserEntityInterface
     */
    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    /**
     * Set associated user.
     *
     * @param int|UserEntityInterface $user Associated user entity or ID
     *
     * @return static
     */
    public function setUser(int|UserEntityInterface $user): static
    {
        if ($user instanceof User) {
            $this->user = $user;
            return $this;
        } elseif ($user instanceof UserEntityInterface) {
            $user = $user->getId();
        }
        $this->user = $this->entityManager->getReference(User::class, $user);
        return $this;
    }

    /**
     * Get the text of the review.
     *
     * @return string
     */
    public function getReview(): string
    {
        return $this->review;
    }

    /**
     * Set the text of the review.
     *
     * @param string $review Review text
     *
     * @return static
     */
    public function setReview(string $review): static
    {
        $this->review = $review;
        return $this;
    }

    /**
     * Is the review approved?
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved === Approved::Yes;
    }

    /**
     * Set whether the review is approved.
     *
     * @param bool $approved Is the review approved?
     *
     * @return static
     */
    public function setIsApproved(bool $approved): static
    {
        $this->approved = $approved ? Approved::Yes : Approved::No;
        return $this;
    }

    /**
     * Get the date the review was added.
     *
     * @return DateTime
     */
    public function getAddedDate(): DateTime
    {
        return $this->added;
    }

    /**
     * Set the date the review was added.
     *
     * @param string|DateTime $date Last login date
     *
     * @return static
     */
    public function setAddedDate(string|DateTime $date): static
    {
        $this->added = $date instanceof DateTime ? $date : DateTime::createFromFormat('Y-m-d', $date);
        return $this;
    }
}
