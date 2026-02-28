<?php

/**
 * Database service for the Tags_Relationships_Values table.
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

use Doctrine\ORM\EntityManager;
use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\TagsRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
use GeebyDeeby\Db\Table\TagsRelationshipsValues;
use GeebyDeeby\ServiceManager\Factory\Autowire;

/**
 * Database service for the Tags_Relationships_Values table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsRelationshipsValueService extends AbstractDbService
{
    /**
     * Constructor
     *
     * @param EntityManager           $entityManager           Entity manager
     * @param PersistenceManager      $persistenceManager      Persistence manager
     * @param TagsRelationshipsValues $relationshipsValueTable TagsRelationshipsValues table
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected TagsRelationshipsValues $relationshipsValueTable
    ) {
        parent::__construct($entityManager, $persistenceManager);
    }

    /**
     * Create an empty entity.
     *
     * @return TagsRelationshipsValueEntityInterface
     */
    public function createEntity(): TagsRelationshipsValueEntityInterface
    {
        return $this->relationshipsValueTable->createRow();
    }

    /**
     * Get a list of tags related to the provided subject tag ID.
     *
     * @param int $tagID Tag ID
     *
     * @return array
     */
    public function getTagsRelatedtoObjectTag(int $tagID): array
    {
        return iterator_to_array($this->relationshipsValueTable->getTagsRelatedtoObjectTag($tagID));
    }

    /**
     * Get a list of tags related to the provided subject tag ID.
     *
     * @param int $tagID Tag ID
     *
     * @return array
     */
    public function getTagsRelatedtoSubjectTag(int $tagID): array
    {
        return iterator_to_array($this->relationshipsValueTable->getTagsRelatedtoSubjectTag($tagID));
    }

    /**
     * Get a list of relationships for the specified tag.
     *
     * @param int $tagID Tag ID
     *
     * @return array
     */
    public function getRelationshipsForTag(int $tagID): array
    {
        return $this->relationshipsValueTable->getRelationshipsForTag($tagID);
    }

    /**
     * Get a row matching the provided source container/contained tag pair.
     *
     * @param int|TagEntityInterface              $subject      Subject tag ID or entity
     * @param int|TagEntityInterface              $object       Object tag ID or entity
     * @param int|TagsRelationshipEntityInterface $relationship Relationship ID or entity
     *
     * @return ?TagsRelationshipsValueEntityInterface
     */
    public function getBySubjectAndObjectAndRelationship(
        int|TagEntityInterface $subject,
        int|TagEntityInterface $object,
        int|TagsRelationshipEntityInterface $relationship
    ): ?TagsRelationshipsValueEntityInterface {
        $where = [
            'Subject_Tag_ID' => $subject instanceof TagEntityInterface ? $subject->getId() : $subject,
            'Object_Tag_ID' => $object instanceof TagEntityInterface ? $object->getId() : $object,
            'Tags_Relationship_ID' => $relationship instanceof TagsRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        foreach ($this->relationshipsValueTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
