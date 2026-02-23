<?php

/**
 * Adapter to bridge gap between Doctrine paginator and Laminas paginator.
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

namespace GeebyDeeby\Db;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Paginator\Adapter\AdapterInterface;

/**
 * Adapter to bridge gap between Doctrine paginator and Laminas paginator.
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class DoctrinePaginatorAdapter implements AdapterInterface
{
    /**
     * Constructor
     *
     * @param Paginator $paginator Doctrine paginator to wrap
     */
    public function __construct(protected Paginator $paginator)
    {
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param int $offset           Page offset
     * @param int $itemCountPerPage Number of items per page
     *
     * @return iterable
     */
    public function getItems($offset, $itemCountPerPage): iterable
    {
        $this->paginator->getQuery()->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        return $this->paginator->getIterator();
    }

    /**
     * Get count for purposes of Countable interface.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->paginator->count();
    }
}
