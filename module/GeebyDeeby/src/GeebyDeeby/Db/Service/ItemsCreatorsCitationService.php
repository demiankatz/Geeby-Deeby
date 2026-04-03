<?php

/**
 * Database service for the Items_Creators_Citations table.
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

use GeebyDeeby\Db\Entity\Citation;
use GeebyDeeby\Db\Entity\CitationEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorsCitation;
use GeebyDeeby\Db\Entity\ItemsCreatorsCitationEntityInterface;

/**
 * Database service for the Items_Creators_Citations table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreatorsCitationService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return ItemsCreatorsCitationEntityInterface
     */
    public function createEntity(): ItemsCreatorsCitationEntityInterface
    {
        $entity = new ItemsCreatorsCitation();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Given a row ID from Items_Creators, return a list of citations
     *
     * @param int $rowID Row ID
     *
     * @return array
     */
    public function getCitations(int $rowID): array
    {
        $dql = 'SELECT c.id AS Citation_ID, c.citationName as Citation '
            . 'FROM ' . ItemsCreatorsCitation::class
            . ' icc JOIN ' . Citation::class . ' c ON icc.citation=c.id '
            . 'WHERE icc.creator = :id ORDER BY c.citationName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('id', $rowID);
        return $query->getResult();
    }

    /**
     * Get an entity by creator and citation.
     *
     * @param int|ItemsCreatorEntityInterface $creator  Creator entity or ID
     * @param int|CitationEntityInterface     $citation Citation entity or ID
     *
     * @return ?ItemsCreatorsCitationEntityInterface
     */
    public function getByCreatorAndCitation(
        int|ItemsCreatorEntityInterface $creator,
        int|CitationEntityInterface $citation
    ): ?ItemsCreatorsCitationEntityInterface {
        $params = [
            'creator' => $creator instanceof ItemsCreatorEntityInterface ? $creator->getId() : $creator,
            'citation' => $citation instanceof CitationEntityInterface ? $citation->getId() : $citation,
        ];
        $dql = 'SELECT icc FROM ' . ItemsCreatorsCitation::class
            . ' icc WHERE icc.creator = :creator AND icc.citation = :citation';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
