<?php

/**
 * Database service for the Editions_OCLC_Numbers table.
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
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Service;

use GeebyDeeby\Db\Entity\Edition;
use GeebyDeeby\Db\Entity\EditionsOclcNumber;
use GeebyDeeby\Db\Entity\EditionsOclcNumberEntityInterface;

/**
 * Database service for the Editions_OCLC_Numbers table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsOclcNumberService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsOclcNumberEntityInterface
     */
    public function createEntity(): EditionsOclcNumberEntityInterface
    {
        $entity = new EditionsOclcNumber();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsOclcNumberEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsOclcNumberEntityInterface
    {
        return $this->entityManager->find(EditionsOclcNumber::class, $id);
    }

    /**
     * Get a list of OCLC Numbers for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsOclcNumberEntityInterface[]
     */
    public function getOCLCNumbersForEdition(int $editionID): array
    {
        $dql = 'SELECT eo FROM ' . EditionsOclcNumber::class
            . ' eo WHERE eo.edition = :edition ORDER BY eo.oclcNumber';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get a list of OCLC Numbers for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsOclcNumberEntityInterface[]
     */
    public function getOCLCNumbersForItem(int $itemID): array
    {
        $dql = 'SELECT eo FROM ' . EditionsOclcNumber::class . ' eo '
            . 'INNER JOIN ' . Edition::class . ' e ON e.id=eo.edition '
            . 'WHERE e.item = :item ORDER BY eo.oclcNumber';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }
}
