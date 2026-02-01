<?php

/**
 * Row Definition for Items_Creators_Citations
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

use GeebyDeeby\Db\Entity\CitationEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorEntityInterface;
use GeebyDeeby\Db\Entity\ItemsCreatorsCitationEntityInterface;

/**
 * Row Definition for Items_Creators_Citations
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ItemsCreatorsCitations extends TableAwareGateway implements ItemsCreatorsCitationEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Item_Creator_ID', 'Citation_ID'], 'Items_Creators_Citations', $adapter);
    }

    /**
     * Get associated creator.
     *
     * @return ItemsCreatorEntityInterface
     */
    public function getCreator(): ItemsCreatorEntityInterface
    {
        return $this->getTableManager()->get('itemscreators')->getByPrimaryKey($this->Item_Creator_ID);
    }

    /**
     * Set associated creator.
     *
     * @param int|ItemsCreatorEntityInterface $creator Associated creator entity or ID
     *
     * @return static
     */
    public function setCreator(int|ItemsCreatorEntityInterface $creator): static
    {
        $this->Item_Creator_ID = $creator instanceof ItemsCreatorEntityInterface ? $creator->getId() : $creator;
        return $this;
    }

    /**
     * Get associated citation.
     *
     * @return CitationEntityInterface
     */
    public function getCitation(): CitationEntityInterface
    {
        return $this->getTableManager()->get('citation')->getByPrimaryKey($this->Citation_ID);
    }

    /**
     * Set associated citation.
     *
     * @param int|CitationEntityInterface $citation Associated citation entity or ID
     *
     * @return static
     */
    public function setCitation(int|CitationEntityInterface $citation): static
    {
        $this->Citation_ID = $citation instanceof CitationEntityInterface ? $citation->getId() : $citation;
        return $this;
    }
}
