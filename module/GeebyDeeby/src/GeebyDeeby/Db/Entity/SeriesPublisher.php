<?php

/**
 * Series/publisher link entity model.
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
 * Series/publisher link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series_Publishers')]
#[ORM\Index(name: 'series', columns: ['Series_ID'])]
#[ORM\Index(name: 'publisher', columns: ['Publisher_ID'])]
#[ORM\Index(name: 'note_id', columns: ['Note_ID'])]
#[ORM\Index(name: 'imprint_id', columns: ['Imprint_ID'])]
#[ORM\Index(name: 'address_id', columns: ['Address_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class SeriesPublisher extends AbstractEntity implements SeriesPublisherEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Series_Publisher_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

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
    #[ORM\ManyToOne(targetEntity: Series::class)]
    protected Series $series;

    /**
     * Publisher.
     *
     * @var Publisher
     */
    #[ORM\JoinColumn(
        name: 'Publisher_ID',
        referencedColumnName: 'Publisher_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    protected Publisher $publisher;

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
     * Imprint.
     *
     * @var ?PublishersImprint
     */
    #[ORM\JoinColumn(
        name: 'Imprint_ID',
        referencedColumnName: 'Imprint_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: PublishersImprint::class)]
    protected ?PublishersImprint $imprint;

    /**
     * Address.
     *
     * @var ?PublishersAddress
     */
    #[ORM\JoinColumn(
        name: 'Address_ID',
        referencedColumnName: 'Address_ID',
        nullable: true,
        options: ['default' => null]
    )]
    #[ORM\ManyToOne(targetEntity: PublishersAddress::class)]
    protected ?PublishersAddress $address;

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
     * Get associated imprint (if any).
     *
     * @return ?PublishersImprintEntityInterface
     */
    public function getImprint(): ?PublishersImprintEntityInterface
    {
        return $this->imprint;
    }

    /**
     * Set associated imprint (if any).
     *
     * @param int|PublishersImprintEntityInterface|null $imprint Associated imprint entity or ID (null for none)
     *
     * @return static
     */
    public function setImprint(int|PublishersImprintEntityInterface|null $imprint): static
    {
        if ($imprint instanceof PublishersImprint || null === $imprint) {
            $this->imprint = $imprint;
            return $this;
        } elseif ($imprint instanceof PublishersImprintEntityInterface) {
            $imprint = $imprint->getId();
        }
        $this->imprint = $this->entityManager->getReference(PublishersImprint::class, $imprint);
        return $this;
    }

    /**
     * Get associated address (if any).
     *
     * @return ?PublishersAddressEntityInterface
     */
    public function getAddress(): ?PublishersAddressEntityInterface
    {
        return $this->address;
    }

    /**
     * Set associated address (if any).
     *
     * @param int|PublishersAddressEntityInterface|null $address Associated address entity or ID (null for none)
     *
     * @return static
     */
    public function setAddress(int|PublishersAddressEntityInterface|null $address): static
    {
        if ($address instanceof PublishersAddress || null === $address) {
            $this->address = $address;
            return $this;
        } elseif ($address instanceof PublishersAddressEntityInterface) {
            $address = $address->getId();
        }
        $this->address = $this->entityManager->getReference(PublishersAddress::class, $address);
        return $this;
    }
}
