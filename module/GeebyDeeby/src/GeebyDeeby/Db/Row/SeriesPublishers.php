<?php

/**
 * Row Definition for Series_Publishers
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\NoteEntityInterface;
use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Entity\PublishersAddressEntityInterface;
use GeebyDeeby\Db\Entity\PublishersImprintEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Entity\SeriesPublisherEntityInterface;

/**
 * Row Definition for Series_Publishers
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesPublishers extends TableAwareGateway implements SeriesPublisherEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(
            'Series_Publisher_ID',
            'Series_Publishers',
            $adapter
        );
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Series_Publisher_ID ?? null;
    }

    /**
     * Get associated series.
     *
     * @return SeriesEntityInterface
     */
    public function getSeries(): SeriesEntityInterface
    {
        return $this->getTableManager()->get('series')->getByPrimaryKey($this->Series_ID);
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
        $this->Series_ID = $series instanceof SeriesEntityInterface ? $series->getId() : $series;
        return $this;
    }

    /**
     * Get associated publisher.
     *
     * @return PublisherEntityInterface
     */
    public function getPublisher(): PublisherEntityInterface
    {
        return $this->getTableManager()->get('publisher')->getByPrimaryKey($this->Publisher_ID);
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
        $this->Publisher_ID = $publisher instanceof PublisherEntityInterface ? $publisher->getId() : $publisher;
        return $this;
    }

    /**
     * Get associated note (if any).
     *
     * @return ?NoteEntityInterface
     */
    public function getNote(): ?NoteEntityInterface
    {
        return $this->Note_ID
            ? $this->getTableManager()->get('note')->getByPrimaryKey($this->Note_ID)
            : null;
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
        $this->Note_ID = $note instanceof NoteEntityInterface ? $note->getId() : $note;
        return $this;
    }

    /**
     * Get associated imprint (if any).
     *
     * @return ?PublishersImprintEntityInterface
     */
    public function getImprint(): ?PublishersImprintEntityInterface
    {
        return $this->Imprint_ID
            ? $this->getTableManager()->get('publishersimprints')->getByPrimaryKey($this->Imprint_ID)
            : null;
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
        $this->Imprint_ID = $imprint instanceof PublishersImprintEntityInterface ? $imprint->getId() : $imprint;
        return $this;
    }

    /**
     * Get associated address (if any).
     *
     * @return ?PublishersAddressEntityInterface
     */
    public function getAddress(): ?PublishersAddressEntityInterface
    {
        return $this->Address_ID
            ? $this->getTableManager()->get('publishersimprints')->getByPrimaryKey($this->Address_ID)
            : null;
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
        $this->Address_ID = $address instanceof PublishersAddressEntityInterface ? $address->getId() : $address;
        return $this;
    }
}
