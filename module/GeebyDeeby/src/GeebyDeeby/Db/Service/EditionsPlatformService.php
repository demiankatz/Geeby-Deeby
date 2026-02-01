<?php

/**
 * Database service for the Editions_Platforms table.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2026.
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsPlatformEntityInterface;
use GeebyDeeby\Db\Entity\PlatformEntityInterface;
use GeebyDeeby\Db\Table\EditionsPlatforms;
use GeebyDeeby\ServiceManager\Factory\Autowire;

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
     * Constructor
     *
     * @param EditionsPlatforms $platformTable EditionsPlatforms table
     */
    public function __construct(
        #[Autowire(container: \GeebyDeeby\Db\Table\PluginManager::class)]
        protected EditionsPlatforms $platformTable
    ) {
    }

    /**
     * Create an empty entity.
     *
     * @return EditionsPlatformEntityInterface
     */
    public function createEntity(): EditionsPlatformEntityInterface
    {
        return $this->platformTable->createRow();
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
        return iterator_to_array($this->platformTable->getItemsForPlatform($platformID));
    }

    /**
     * Get a list of platforms for the specified item.
     *
     * @param int $itemID Item ID
     *
     * @return array
     */
    public function getPlatformsForItem(int $itemID): array
    {
        return iterator_to_array($this->platformTable->getPlatformsForItem($itemID));
    }

    /**
     * Get a list of platforms for the specified edition.
     *
     * @param int $editionID Edition ID
     *
     * @return array
     */
    public function getPlatformsForEdition(int $editionID): array
    {
        return iterator_to_array($this->platformTable->getPlatformsForEdition($editionID));
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
        $where = [
            'Edition_ID' => $edition instanceof EditionEntityInterface ? $edition->getId() : $edition,
            'Platform_ID' => $platform instanceof PlatformEntityInterface ? $platform->getId() : $platform,
        ];
        foreach ($this->platformTable->select($where) as $row) {
            return $row;
        }
        return null;
    }
}
