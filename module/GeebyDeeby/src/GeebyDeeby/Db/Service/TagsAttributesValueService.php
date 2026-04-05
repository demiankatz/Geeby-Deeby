<?php

/**
 * Database service for the Tags_Attributes_Values table.
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

use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsAttribute;
use GeebyDeeby\Db\Entity\TagsAttributesValue;
use GeebyDeeby\Db\Entity\TagsAttributesValueEntityInterface;

/**
 * Database service for the Tags_Attributes_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsAttributesValueService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return TagsAttributesValueEntityInterface
     */
    public function createEntity(): TagsAttributesValueEntityInterface
    {
        $entity = new TagsAttributesValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of attributes for the specified tag.
     *
     * @param int $tagID Tag ID
     *
     * @return array
     */
    public function getAttributesForTag(int $tagID): array
    {
        $dql = 'SELECT tav.value AS Tags_Attribute_Value, ta.id AS Tags_Attribute_ID, '
            . 'ta.attributeName AS Tags_Attribute_Name, ta.rdfProperty AS Tags_Attribute_RDF_Property, '
            . 'ta.allowHtml AS Allow_HTML, ta.displayPriority AS Display_Priority FROM '
            . TagsAttributesValue::class . ' tav '
            . 'INNER JOIN ' . TagsAttribute::class . ' ta ON tav.attribute=ta.id '
            . 'WHERE tav.tag = :tag ORDER BY ta.displayPriority, ta.attributeName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tagID);
        return $query->getResult();
    }

    /**
     * Delete existing attributes associated with the provided tag.
     *
     * @param int|TagEntityInterface $tag Tag entity or ID
     *
     * @return void
     */
    public function deleteByTag(int|TagEntityInterface $tag): void
    {
        $dql = 'DELETE FROM ' . TagsAttributesValue::class . ' tav WHERE tav.tag=:tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tag instanceof TagEntityInterface ? $tag->getId() : $tag);
        $query->execute();
    }
}
