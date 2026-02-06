<?php

/**
 * Interface for item creator citation entity models.
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
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity;

/**
 * Interface for item creator citation entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface ItemsCreatorsCitationEntityInterface extends EntityInterface
{
    /**
     * Get associated creator.
     *
     * @return ItemsCreatorEntityInterface
     */
    public function getCreator(): ItemsCreatorEntityInterface;

    /**
     * Set associated creator.
     *
     * @param int|ItemsCreatorEntityInterface $creator Associated creator entity or ID
     *
     * @return static
     */
    public function setCreator(int|ItemsCreatorEntityInterface $creator): static;

    /**
     * Get associated citation.
     *
     * @return CitationEntityInterface
     */
    public function getCitation(): CitationEntityInterface;

    /**
     * Set associated citation.
     *
     * @param int|CitationEntityInterface $citation Associated citation entity or ID
     *
     * @return static
     */
    public function setCitation(int|CitationEntityInterface $citation): static;
}
