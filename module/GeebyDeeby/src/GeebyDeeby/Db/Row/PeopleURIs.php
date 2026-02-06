<?php

/**
 * Row Definition for People_URIs
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

use GeebyDeeby\Db\Entity\PeopleUriEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\PredicateEntityInterface;

/**
 * Row Definition for People_URIs
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleURIs extends TableAwareGateway implements PeopleUriEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'People_URIs', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Sequence_ID ?? null;
    }

    /**
     * Get associated person.
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): PersonEntityInterface
    {
        return $this->getTableManager()->get('person')->getByPrimaryKey($this->Person_ID);
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
        $this->Person_ID = $person instanceof PersonEntityInterface ? $person->getId() : $person;
        return $this;
    }

    /**
     * Get associated predicate.
     *
     * @return PredicateEntityInterface
     */
    public function getPredicate(): PredicateEntityInterface
    {
        return $this->getTableManager()->get('predicate')->getByPrimaryKey($this->Predicate_ID);
    }

    /**
     * Set associated predicate.
     *
     * @param int|PredicateEntityInterface $predicate Associated predicate entity or ID
     *
     * @return static
     */
    public function setPredicate(int|PredicateEntityInterface $predicate): static
    {
        $this->Predicate_ID = $predicate instanceof PredicateEntityInterface ? $predicate->getId() : $predicate;
        return $this;
    }

    /**
     * Get the URI associated with the person.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->URI;
    }

    /**
     * Set the URI associated with the person.
     *
     * @param string $uri New URI.
     *
     * @return static
     */
    public function setUri(string $uri): static
    {
        $this->URI = $uri;
        return $this;
    }
}
