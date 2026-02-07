<?php

/**
 * Local extensions to database service for the Series table.
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
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Db\Service;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

/**
 * Local extensions to database service for the Series table.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SeriesService extends \GeebyDeeby\Db\Service\SeriesService
{
    /**
     * Retrieve progress statistics.
     *
     * @return array
     */
    public function getProgressStatistics(): array
    {
        $callback = function ($select): void {
            $select->columns(
                [
                    'Series_ID' => 'Series_ID',
                    'Series_Name' => 'Series_Name',
                    'Item_Count' => new Expression(
                        'count(distinct(?))',
                        ['Item_ID'],
                        [Expression::TYPE_IDENTIFIER]
                    ),
                    'Complete' => new Expression(
                        'if (Series_Description like \'%in progress%\', 0, 1)'
                    ),
                ]
            );
            $select->join(
                ['e' => 'Editions'],
                'Series.Series_ID = e.Series_ID',
                [],
                Select::JOIN_LEFT
            );
            $select->join(
                ['source' => 'Series_Attributes_Values'],
                new Expression(
                    'Series.Series_ID = source.Series_ID '
                    . 'AND source.Series_Attribute_ID=16'
                ),
                ['source' => 'Series_Attribute_Value'],
                Select::JOIN_LEFT
            );
            $select->join(
                ['status' => 'Series_Attributes_Values'],
                new Expression(
                    'Series.Series_ID = status.Series_ID '
                    . 'AND status.Series_Attribute_ID=17'
                ),
                ['status' => 'Series_Attribute_Value'],
                Select::JOIN_LEFT
            );
            $select->group(['Series.Series_ID']);
            $select->where(['e.Parent_Edition_ID' => null]);
            $select->order(['Series.Series_Name']);
        };
        return $this->seriesTable->select($callback)->toArray();
    }
}
