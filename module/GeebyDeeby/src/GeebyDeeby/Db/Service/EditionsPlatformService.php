<?php

/**
 * Database service for the Editions_Platforms table.
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
use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsPlatform;
use GeebyDeeby\Db\Entity\EditionsPlatformEntityInterface;
use GeebyDeeby\Db\Entity\Item;
use GeebyDeeby\Db\Entity\Platform;
use GeebyDeeby\Db\Entity\PlatformEntityInterface;
use GeebyDeeby\Db\Entity\Series;

/**
 * Database service for the Editions_Platforms table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsPlatformService extends AbstractDbService
{
    /**
     * Create an empty entity.
     *
     * @return EditionsPlatformEntityInterface
     */
    public function createEntity(): EditionsPlatformEntityInterface
    {
        $entity = new EditionsPlatform();
        $entity->setEntityManager($this->entityManager);
        return $entity;
    }

    /**
     * Get editions for the specified platform.
     *
     * @param int $platformID Platform ID
     *
     * @return array
     */
    public function getItemsForPlatform(int $platformID): array
    {
        $dql = 'SELECT  e.volume AS Volume, e.position AS Position, e.replacementNumber AS Replacement_Number, '
            . 'i.itemName AS Item_Name, i.id AS Item_ID, '
            . 's.seriesName AS Series_Name, s.id AS Series_ID '
            . ' FROM ' . EditionsPlatform::class . ' ep '
            . 'INNER JOIN ' . Edition::class . ' e ON ep.edition=e.id '
            . 'INNER JOIN ' . Item::class . ' i ON e.item=i.id '
            . 'INNER JOIN ' . Series::class . ' s ON e.series=s.id '
            . 'WHERE ep.platform = :platform '
            . 'GROUP BY s.seriesName, i.id, s.id, e.volume, e.position, e.replacementNumber, i.itemName '
            . 'ORDER BY s.seriesName, s.id, e.volume, e.position, e.replacementNumber, i.itemName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('platform', $platformID);
        return $query->getResult();
    }

    /**
     * Get a list of platforms for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return EditionsPlatformEntityInterface[]
     */
    public function getPlatformsForItem(int $itemID): array
    {
        $dql = 'SELECT ep FROM ' . EditionsPlatform::class
            . ' ep INNER JOIN ' . Platform::class . ' p ON ep.platform=p.id '
            . 'INNER JOIN ' . Edition::class . ' e ON ep.edition=e.id '
            . 'WHERE e.item = :item ORDER BY p.platformName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('item', $itemID);
        return $query->getResult();
    }

    /**
     * Get a list of platforms for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return EditionsPlatformEntityInterface[]
     */
    public function getPlatformsForEdition(int $editionID): array
    {
        $dql = 'SELECT ep FROM ' . EditionsPlatform::class
            . ' ep INNER JOIN ' . Platform::class . ' p ON ep.platform=p.id '
            . 'WHERE ep.edition = :edition ORDER BY p.platformName';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('edition', $editionID);
        return $query->getResult();
    }

    /**
     * Get an entity by edition and platform.
     *
     * @param int|EditionEntityInterface  $edition  Edition entity or ID
     * @param int|PlatformEntityInterface $platform Platform entity or ID
     *
     * @return ?EditionsPlatformEntityInterface
     */
    public function getByEditionAndPlatform(
        int|EditionEntityInterface $edition,
        int|PlatformEntityInterface $platform
    ): ?EditionsPlatformEntityInterface {
        $editionId = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        $platformId = $platform instanceof PlatformEntityInterface ? $platform->getId() : $platform;
        $dql = 'SELECT ep FROM ' . EditionsPlatform::class
            . ' ep WHERE ep.edition = :edition AND ep.platform = :platform';
        $query = $this->entityManager->createQuery($dql);
        $query->setParameters(['platform' => $platformId, 'edition' => $editionId]);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }
}
