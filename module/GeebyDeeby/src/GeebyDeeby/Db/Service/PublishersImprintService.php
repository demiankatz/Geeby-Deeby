<?php

/**
 * Database service for the Publishers_Imprints table.
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

use GeebyDeeby\Db\Entity\PublishersImprint;
use GeebyDeeby\Db\Entity\PublishersImprintEntityInterface;

/**
 * Database service for the Publishers_Imprints table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PublishersImprintService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return PublishersImprintEntityInterface
     */
    public function createEntity(): PublishersImprintEntityInterface
    {
        $entity = new PublishersImprint();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of imprints for the specified publisher.
     *
     * @param int $pubID Publisher ID
     *
     * @return array
     */
    public function getImprintsForPublisher(int $pubID): array
    {
        $dql = 'SELECT pi.id AS Imprint_ID, pi.imprintName AS Imprint_Name '
            . 'FROM ' . PublishersImprint::class . ' pi '
            . 'WHERE pi.publisher = :publisher ORDER BY pi.imprintName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('publisher', $pubID);
        return $query->getResult();
    }

    /**
     * Retrieve a specific imprint for a specific publisher.
     *
     * @param int $publisherId Publisher ID
     * @param int $imprintId   Imprint ID
     *
     * @return ?
     */
    public function getLinkByPublisherAndImprint(
        int $publisherId,
        int $imprintId
    ): ?PublishersImprintEntityInterface {
        $dql = 'SELECT p FROM ' . PublishersImprint::class . ' p WHERE p.publisher = :publisher AND p.id = :imprint';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['publisher' => $publisherId, 'imprint' => $imprintId]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
