<?php

/**
 * Database service for the Editions_Product_Codes table.
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
use GeebyDeeby\Db\Entity\EditionsProductCode;
use GeebyDeeby\Db\Entity\EditionsProductCodeEntityInterface;

/**
 * Database service for the Editions_Product_Codes table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsProductCodeService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsProductCodeEntityInterface
     */
    public function createEntity(): EditionsProductCodeEntityInterface
    {
        $entity = new EditionsProductCode();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Retrieve a row by its primary key.
     *
     * @param int $id Identifier to retrieve
     *
     * @return ?EditionsProductCodeEntityInterface
     */
    public function getByPrimaryKey(int $id): ?EditionsProductCodeEntityInterface
    {
        return $this->entityManager->find(EditionsProductCode::class, $id);
    }

    /**
     * Get a list of OCLC Numbers for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsProductCodeEntityInterface[]
     */
    public function getProductCodesForEdition(int $editionID): array
    {
        $dql = 'SELECT ep FROM ' . EditionsProductCode::class
            . ' ep WHERE ep.edition = :edition ORDER BY ep.productCode';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get a list of OCLC Numbers for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsProductCodeEntityInterface[]
     */
    public function getProductCodesForItem(int $itemID): array
    {
        $dql = 'SELECT ep FROM ' . EditionsProductCode::class . ' ep '
            . 'INNER JOIN ' . Edition::class . ' e ON e.id=ep.edition '
            . 'WHERE e.item = :item ORDER BY ep.productCode';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }
}
