<?php

/**
 * Interface for tag attribute value entity models.
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
 * Interface for tag attribute value entity models.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
interface TagsAttributesValueEntityInterface extends EntityInterface
{
    /**
     * Get associated tag.
     *
     * @return TagEntityInterface
     */
    public function getTag(): TagEntityInterface;

    /**
     * Set associated tag.
     *
     * @param int|TagEntityInterface $tag Associated tag entity or ID
     *
     * @return static
     */
    public function setTag(int|TagEntityInterface $tag): static;

    /**
     * Get associated attribute.
     *
     * @return TagsAttributeEntityInterface
     */
    public function getAttribute(): TagsAttributeEntityInterface;

    /**
     * Set associated attribute.
     *
     * @param int|TagsAttributeEntityInterface $attribute Associated attribute entity or ID
     *
     * @return static
     */
    public function setAttribute(int|TagsAttributeEntityInterface $attribute): static;

    /**
     * Get the value of the attribute.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Set the value of the attribute.
     *
     * @param string $value New value
     *
     * @return static
     */
    public function setValue(string $value): static;
}
