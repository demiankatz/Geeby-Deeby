<?php

/**
 * Row Definition for Publishers_Imprints
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\PublisherEntityInterface;
use GeebyDeeby\Db\Entity\PublishersImprintEntityInterface;

/**
 * Row Definition for Publishers_Imprints
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersImprints extends TableAwareGateway implements PublishersImprintEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Imprint_ID', 'Publishers_Imprints', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Imprint_ID ?? null;
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
     * Get the name of the imprint.
     *
     * @return string
     */
    public function getImprintName(): string
    {
        return $this->Imprint_Name;
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
        $this->Imprint_Name = $name;
        return $this;
    }
}
