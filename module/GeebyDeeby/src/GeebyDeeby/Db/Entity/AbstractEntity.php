<?php

/**
 * Trait for exporting entities as arrays.
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

use ArrayAccess;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Exception;
use ReflectionClass;

/**
 * Trait for exporting entities as arrays.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
abstract class AbstractEntity implements ArrayAccess, EntityInterface
{
    /**
     * Return an array representation of the entity
     *
     * @return array
     */
    public function toArray(): array
    {
        $vals = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attrs = $property->getAttributes(Column::class);
            if ($attrs && ($args = $attrs[0]->getArguments()) && $args['name']) {
                $vals[$args['name']] = $property->getValue($this);
            }
        }
        return $vals;
    }

    /**
     * Get an array of primary key fields for the entity.
     *
     * @return array
     */
    public function getPrimaryKeyColumn(): array
    {
        $vals = [];
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            $attrs = $property->getAttributes(Column::class);
            if (
                $attrs && ($args = $attrs[0]->getArguments()) && $args['name'] && $property->getAttributes(Id::class)
            ) {
                $vals[] = $args['name'];
            }
        }
        return $vals;
    }

    /**
     * Set a value at the specified offset.
     *
     * @param mixed $offset Offset
     * @param mixed $value  Value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        throw new Exception("Cannot set $offset to $value.");
    }

    /**
     * Unset the specified offset.
     *
     * @param mixed $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        throw new Exception("Cannot unset $offset");
    }

    /**
     * Does the specified offset exist?
     *
     * @param mixed $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    /**
     * Get a value from the specified offset.
     *
     * @param mixed $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->toArray()[$offset];
    }
}
