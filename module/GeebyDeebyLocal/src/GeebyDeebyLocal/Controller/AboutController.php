<?php
/**
 * About controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

/**
 * About controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AboutController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Credits page
     *
     * @return mixed
     */
    public function creditsAction()
    {
        return $this->createViewModel();
    }

    /**
     * About page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->createViewModel();
    }

    /**
     * Progress page
     *
     * @return mixed
     */
    public function progressAction()
    {
        $stats = $this->getProgressStatistics();
        return $this->createViewModel(['progress' => $stats]);
    }

    /**
     * Retrieve progress statistics.
     *
     * @return array
     */
    protected function getProgressStatistics()
    {
        $s = $this->getDbTable('series');
        $callback = function ($select) {
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
                new \Laminas\Db\Sql\Expression(
                    'Series.Series_ID = source.Series_ID '
                    . 'AND source.Series_Attribute_ID=16'
                ),
                ['source' => 'Series_Attribute_Value'],
                Select::JOIN_LEFT
            );
            $select->join(
                ['status' => 'Series_Attributes_Values'],
                new \Laminas\Db\Sql\Expression(
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
        return $s->select($callback)->toArray();
    }
}
