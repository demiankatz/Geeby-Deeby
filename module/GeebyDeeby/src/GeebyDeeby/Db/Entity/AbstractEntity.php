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
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Exception;
use ReflectionClass;

use function count;
use function is_callable;

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
            $attrs = $property->getAttributes(Column::class) ?: $property->getAttributes(JoinColumn::class);
            if ($attrs && ($args = $attrs[0]->getArguments()) && $args['name']) {
                $name = $args['name'];
                $value = $property->getValue($this);
                if (str_ends_with($args['name'], '_ID') && is_callable([$value, 'getId'])) {
                    $vals[$name] = $value->getId();
                    $name = str_replace('_ID', '_Object', $name);
                }
                if ($value instanceof DateTime) {
                    $vals[$name . 'Object'] = $value;
                    $value = $value->format('Y-m-d h:i:s');
                }
                $vals[$name] = $value;
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
     * Get primary key value for the table
     *
     * @return int
     */
    public function getPrimaryKeyValue(): int
    {
        $keyCols = $this->getPrimaryKeyColumn();
        if (count($keyCols) != 1) {
            throw new \Exception('Unsupported for multi-key tables');
        }
        return $this->offsetGet($keyCols[0]);
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
