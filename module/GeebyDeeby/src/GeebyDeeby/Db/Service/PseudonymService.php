<?php

/**
 * Database service for the Pseudonyms table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use GeebyDeeby\Db\Entity\PersonEntityInterface;
use GeebyDeeby\Db\Entity\PseudonymEntityInterface;
use GeebyDeeby\Db\Table\Pseudonyms;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Pseudonyms table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PseudonymService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param Pseudonyms $pseudonymsTable Pseudonyms table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected Pseudonyms $pseudonymsTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return PseudonymEntityInterface
     */
    public function createEntity(): PseudonymEntityInterface
    {
        return $this->pseudonymsTable->createRow();
    }

    /**
     * Get a list of pseudonyms for a real name.
     *
     * @param int $personID Real person ID
     *
     * @return array
     */
    public function getPseudonyms(int $personID): array
    {
        return iterator_to_array($this->pseudonymsTable->getPseudonyms($personID));
    }

    /**
     * Get a list of real names for a pseudonym.
     *
     * @param int $personID Pseudonym ID
     *
     * @return array
     */
    public function getRealNames(int $personID): array
    {
        return iterator_to_array($this->pseudonymsTable->getRealNames($personID));
    }

    /**
     * Get a batch of real name information keyed by ID.
     *
     * @param iterable $people Collection of people to look up
     *
     * @return array
     */
    public function getRealNamesBatch(iterable $people): array
    {
        return $this->pseudonymsTable->getRealNamesBatch($people);
    }

    /**
     * Retrieve a link using a real person and a pseudonym.
     *
     * @param int|PersonEntityInterface $real   ID or person entity for real person
     * @param int|PersonEntityInterface $pseudo ID or person entity for pseudonym
     *
     * @return ?PseudonymEntityInterface
     */
    public function getByRealPersonAndPseudonym(
        int|PersonEntityInterface $real,
        int|PersonEntityInterface $pseudo
    ): ?PseudonymEntityInterface {
        $where = [
            'Real_Person_ID' => $real instanceof PersonEntityInterface ? $real->getId() : $real,
            'Pseudo_Person_ID' => $pseudo instanceof PersonEntityInterface ? $pseudo->getId() : $pseudo,
        ];
        foreach ($this->pseudonymsTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
