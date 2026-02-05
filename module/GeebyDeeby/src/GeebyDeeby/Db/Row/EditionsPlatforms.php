<?php

/**
 * Row Definition for Editions_Platforms
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsPlatformEntityInterface;
use GeebyDeeby\Db\Entity\PlatformEntityInterface;

/**
 * Row Definition for Editions_Platforms
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsPlatforms extends TableAwareGateway implements EditionsPlatformEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Edition_ID', 'Platform_ID'], 'Editions_Platforms', $adapter);
    }

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->getTableManager()->get('edition')->getByPrimaryKey($this->Edition_ID);
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static
    {
        $this->Edition_ID = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        return $this;
    }

    /**
     * Get associated platform.
     *
     * @return PlatformEntityInterface
     */
    public function getPlatform(): PlatformEntityInterface
    {
        return $this->getTableManager()->get('platform')->getByPrimaryKey($this->Platform_ID);
    }

    /**
     * Set associated platform.
     *
     * @param int|PlatformEntityInterface $platform Associated platform entity or ID
     *
     * @return static
     */
    public function setPlatform(int|PlatformEntityInterface $platform): static
    {
        $this->Platform_ID = $platform instanceof PlatformEntityInterface ? $platform->getId() : $platform;
        return $this;
    }
}
