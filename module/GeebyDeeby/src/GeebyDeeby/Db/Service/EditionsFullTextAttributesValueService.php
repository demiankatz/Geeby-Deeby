<?php

/**
 * Database service for the Editions_Full_Text_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\EditionsFullText;
use GeebyDeeby\Db\Entity\EditionsFullTextAttribute;
use GeebyDeeby\Db\Entity\EditionsFullTextAttributesValue;
use GeebyDeeby\Db\Entity\EditionsFullTextAttributesValueEntityInterface;
use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;

/**
 * Database service for the Editions_Full_Text_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullTextAttributesValueService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsFullTextAttributesValueEntityInterface
     */
    public function createEntity(): EditionsFullTextAttributesValueEntityInterface
    {
        $entity = new EditionsFullTextAttributesValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of attributes for the specified full text ID(s).
     *
     * @param int|int[] $fullTextID Full text ID (or array of IDs)
     *
     * @return mixed
     */
    public function getAttributesForFullTextIDs(int|array $fullTextID)
    {
        $dql = 'SELECT e.id AS Editions_Full_Text_ID, eftav.value AS Editions_Full_Text_Attribute_Value, '
            . 'efta.id AS Editions_Full_Text_Attribute_ID, '
            . 'efta.attributeName AS Editions_Full_Text_Attribute_Name, '
            . 'efta.rdfProperty AS Editions_Full_Text_Attribute_RDF_Property, '
            . 'efta.allowHtml AS Allow_HTML, efta.displayPriority AS Display_Priority FROM '
            . EditionsFullTextAttributesValue::class . ' eftav '
            . 'INNER JOIN ' . EditionsFullTextAttribute::class . ' efta ON eftav.attribute=efta.id '
            . 'INNER JOIN ' . EditionsFullText::class . ' e ON eftav.fullText=e.id '
            . 'WHERE eftav.fullText IN (:ids) ORDER BY efta.displayPriority, efta.attributeName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('ids', (array)$fullTextID);
        return $query->getResult();
    }

    /**
     * Delete existing attributes associated with the provided edition full text entry.
     *
     * @param int|EditionsFullTextEntityInterface $eft Edition full text entity or ID
     *
     * @return void
     */
    public function deleteByEditionFullText(int|EditionsFullTextEntityInterface $eft): void
    {
        $dql = 'DELETE FROM ' . EditionsFullTextAttributesValue::class . ' e WHERE e.fullText=:id';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('id', $eft instanceof EditionsFullTextEntityInterface ? $eft->getId() : $eft);
        $query->execute();
    }
}
