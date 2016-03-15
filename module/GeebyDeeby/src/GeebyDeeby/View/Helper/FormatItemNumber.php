<?php
/**
 * Item number view helper
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2016.
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

/**
 * Item number view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FormatItemNumber extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Format date information for display
     *
     * @param array  $arr     Edition information
     * @param string $prefix  Prefix to apply to vol./no. when present.
     * @param string $suffix  Suffix to apply to vol./no. when present.
     * @param string $default Default string to return when no numbering found.
     *
     * @return string
     */
    public function __invoke($arr, $prefix = '', $suffix = '. ', $default = '')
    {
        $str = $arr['Volume'] > 0
            ? 'v. ' . $arr['Volume'] . ', no. ' : '';
        if ($arr['Position'] > 0) {
            $str .= $arr['Position'];
            $str .= $arr['Replacement_Number'] > 0
                ? ' (replacement title)' : '';
        }
        return empty($str) ? $default : $prefix . $str . $suffix;
    }
}
