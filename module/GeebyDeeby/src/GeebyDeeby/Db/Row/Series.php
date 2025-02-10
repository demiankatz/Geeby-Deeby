<?php

/**
 * Row Definition for Series
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2012.
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use function strlen;

/**
 * Row Definition for Series
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Series extends TableAwareGateway
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Series_ID', 'Series', $adapter);
    }

    /**
     * Get category information for this series.
     *
     * @return array Integer IDs for all categories associated with the series.
     */
    public function getCategoryIDs()
    {
        $categories = $this->getDbTable('seriescategories')
            ->getCategories($this->Series_ID);
        return array_map(
            function ($current) {
                return $current['Category_ID'];
            },
            $categories->toArray()
        );
    }

    /**
     * Validate the fields in the current object.  Return error message if problem
     * found, boolean false if no errors were found.
     *
     * @return string|bool
     */
    public function validate()
    {
        if (strlen(trim($this->Series_Name)) == 0) {
            return 'Series name cannot be blank.';
        }
        return false;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->Series_Name;
    }
}
