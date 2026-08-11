<?php

/**
 * Publisher address entity model.
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
 * Publisher address entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Publishers_Imprints')]
#[ORM\Index(name: 'publisher_id', columns: ['Publisher_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class PublishersImprint extends AbstractEntity implements PublishersImprintEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Imprint_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Publisher.
     *
     * @var Publisher
     */
    #[ORM\JoinColumn(name: 'Publisher_ID', referencedColumnName: 'Publisher_ID', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    protected Publisher $publisher;

    /**
     * Imprint name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Imprint_Name', type: 'text', length: 255, nullable: false)]
    protected string $imprintName;

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
     * Get associated publisher.
     *
     * @return PublisherEntityInterface
     */
    public function getPublisher(): PublisherEntityInterface
    {
        return $this->publisher;
    }

    /**
     * Set associated publisher.
     *
     * @param int|PublisherEntityInterface $publisher Associated publisher entity or ID
     *
     * @return static
     */
    public function setPublisher(int|PublisherEntityInterface $publisher): static
    {
        if ($publisher instanceof Publisher) {
            $this->publisher = $publisher;
            return $this;
        } elseif ($publisher instanceof PublisherEntityInterface) {
            $publisher = $publisher->getId();
        }
        $this->publisher = $this->entityManager->getReference(Publisher::class, $publisher);
        return $this;
    }

    /**
     * Get the name of the imprint.
     *
     * @return string
     */
    public function getImprintName(): string
    {
        return $this->imprintName;
    }

    /**
     * Set the name of the imprint.
     *
     * @param string $name New name
     *
     * @return static
     */
    public function setImprintName(string $name): static
    {
        $this->imprintName = $name;
        return $this;
    }
}
