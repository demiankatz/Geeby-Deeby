<?php

/**
 * Interface for series relationship value entity models.
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
 * Interface for series relationship value entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface SeriesRelationshipsValueEntityInterface extends EntityInterface
{
    /**
     * Get subject series.
     *
     * @return SeriesEntityInterface
     */
    public function getSubject(): SeriesEntityInterface;

    /**
     * Set subject series.
     *
     * @param int|SeriesEntityInterface $series Subject series entity or ID
     *
     * @return static
     */
    public function setSubject(int|SeriesEntityInterface $series): static;

    /**
     * Get relationship.
     *
     * @return SeriesRelationshipEntityInterface
     */
    public function getRelationship(): SeriesRelationshipEntityInterface;

    /**
     * Set relationship.
     *
     * @param int|SeriesRelationshipEntityInterface $relationship Relationship entity or ID
     *
     * @return static
     */
    public function setRelationship(int|SeriesRelationshipEntityInterface $relationship): static;

    /**
     * Get object series.
     *
     * @return SeriesEntityInterface
     */
    public function getObject(): SeriesEntityInterface;

    /**
     * Set object series.
     *
     * @param int|SeriesEntityInterface $series Object series entity or ID
     *
     * @return static
     */
    public function setObject(int|SeriesEntityInterface $series): static;
}
