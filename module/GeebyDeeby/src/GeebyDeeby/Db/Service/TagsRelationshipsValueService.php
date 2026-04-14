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
use GeebyDeeby\Db\Entity\Tag;
use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsRelationship;
use GeebyDeeby\Db\Entity\TagsRelationshipEntityInterface;
use GeebyDeeby\Db\Entity\TagsRelationshipsValue;
use GeebyDeeby\Db\Entity\TagsRelationshipsValueEntityInterface;
use GeebyDeeby\Db\PersistenceManager;
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
     * @param TagsRelationshipService $tagsRelationshipService TagsRelationship database service
     */
    public function __construct(
        EntityManager $entityManager,
        PersistenceManager $persistenceManager,
        #[Autowire(container: \GeebyDeeby\Db\Service\PluginManager::class)]
        protected TagsRelationshipService $tagsRelationshipService
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
        $entity = new TagsRelationshipsValue();
        $entity->setEntityManager($this->entityManager);
        return $entity;
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
        $dql = 'SELECT t.id AS Tag_ID, t.tag AS Tag, r.id AS Tags_Relationship_ID '
            . 'FROM ' . TagsRelationshipsValue::class
            . ' rv INNER JOIN ' . Tag::class . ' t ON rv.subjectTag=t.id '
            . 'INNER JOIN ' . TagsRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.objectTag=:tag ORDER BY t.tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tagID);
        return $query->getResult();
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
        $dql = 'SELECT t.id AS Tag_ID, t.tag AS Tag, r.id AS Tags_Relationship_ID '
            . 'FROM ' . TagsRelationshipsValue::class
            . ' rv INNER JOIN ' . Tag::class . ' t ON rv.objectTag=t.id '
            . 'INNER JOIN ' . TagsRelationship::class . ' r ON rv.relationship=r.id '
            . 'WHERE rv.subjectTag=:tag ORDER BY t.tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tagID);
        return $query->getResult();
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
        // Collect forward and inverse relationships in an index:
        $index = [];
        $subjectList = $this->getTagsRelatedtoSubjectTag($tagID);
        foreach ($subjectList as $current) {
            $index[$current['Tags_Relationship_ID']][] = $current;
        }
        $objectList = $this->getTagsRelatedtoObjectTag($tagID);
        foreach ($objectList as $current) {
            $index['i' . $current['Tags_Relationship_ID']][] = $current;
        }

        // Look up all options on the option list in the index to build return value:
        $retVal = [];
        $optionList = $this->tagsRelationshipService->getOptionList(true);
        foreach ($optionList as $id => $relationship) {
            if (isset($index[$id])) {
                $retVal[] = $relationship + [
                    'relationship_id' => $id,
                    'values' => $index[$id],
                ];
            }
        }
        return $retVal;
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
        $params = [
            'subject' => $subject instanceof TagEntityInterface ? $subject->getId() : $subject,
            'object' => $object instanceof TagEntityInterface ? $object->getId() : $object,
            'relationship' => $relationship instanceof TagsRelationshipEntityInterface
                ? $relationship->getId() : $relationship,
        ];
        $dql = 'SELECT r FROM ' . TagsRelationshipsValue::class
            . ' r WHERE r.subjectTag=:subject AND r.objectTag=:object AND r.relationship=:relationship';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters($params);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
