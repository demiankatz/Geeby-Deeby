<?php

/**
 * Database service for the Tags_URIs table.
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

use GeebyDeeby\Db\Entity\Predicate;
use GeebyDeeby\Db\Entity\Tag;
use GeebyDeeby\Db\Entity\TagEntityInterface;
use GeebyDeeby\Db\Entity\TagsUri;
use GeebyDeeby\Db\Entity\TagsUriEntityInterface;

/**
 * Database service for the Tags_URIs table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class TagsUriService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return TagsUriEntityInterface
     */
    public function createEntity(): TagsUriEntityInterface
    {
        $entity = new TagsUri();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get a list of tags for the specified URI.
     *
     * @param string $uri URI
     *
     * @return array
     */
    public function getTagsForURI(string $uri): array
    {
        $dql = 'SELECT tu.id AS Sequence_ID, tu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev, '
            . 't.id AS Tag_ID, t.tag as Tag'
            . ' FROM ' . TagsUri::class . ' tu INNER JOIN ' . Predicate::class . ' p ON tu.predicate = p.id'
            . ' INNER JOIN ' . Tag::class . ' t ON tu.tag = t.id'
            . ' WHERE tu.uri = :uri ORDER BY t.tag';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('uri', $uri);
        return $query->getResult();
    }

    /**
     * Get a list of URIs for the specified tag.
     *
     * @param int|TagEntityInterface $tag Tag entity or ID
     *
     * @return array
     */
    public function getURIsForTag(int|TagEntityInterface $tag): array
    {
        $dql = 'SELECT tu.id AS Sequence_ID, tu.uri AS URI, '
            . 'p.id AS Predicate_ID, p.predicate AS Predicate, p.abbreviation AS Predicate_Abbrev '
            . ' FROM ' . TagsUri::class . ' tu INNER JOIN ' . Predicate::class . ' p ON tu.predicate = p.id'
            . ' WHERE tu.tag = :tag ORDER BY tu.uri, p.abbreviation';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('tag', $tag instanceof TagEntityInterface ? $tag->getId() : $tag);
        return $query->getResult();
    }

    /**
     * Retrieve an existing entry using a tag ID and URI (null if not found).
     *
     * @param int    $tagId Tag ID
     * @param string $uri   URI
     *
     * @return ?TagsUriEntityInterface
     */
    public function getByTagAndUri(int $tagId, string $uri): ?TagsUriEntityInterface
    {
        $dql = 'SELECT tu FROM ' . TagsUri::class . ' tu WHERE tu.tag = :tag AND tu.uri = :uri';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['tag' => $tagId, 'uri' => $uri]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
