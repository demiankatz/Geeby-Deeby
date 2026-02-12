<?php

/**
 * Release date view helper
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\Db\Entity\EditionsReleaseDateEntityInterface;
use GeebyDeeby\ServiceManager\Factory\Autowire;

use function is_array;

/**
 * Release date view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FormatReleaseDate
{
    /**
     * Month information
     *
     * @var array
     */
    protected $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /**
     * Constructor
     *
     * @param FixTitle $fixTitleHelper FixTitle view helper.
     */
    public function __construct(
        #[Autowire(container: 'ViewHelperManager')]
        protected FixTitle $fixTitleHelper
    ) {
    }

    /**
     * Format date information for display
     *
     * @param array|EditionsReleaseDateEntityInterface $date        Date information
     * @param bool                                     $showEdition Show edition information
     *
     * @return string
     */
    public function __invoke(array|EditionsReleaseDateEntityInterface $date, bool $showEdition = false): string
    {
        if (is_array($date)) {
            $arr = $date;
        } else {
            $arr = $date->toArray();
            $arr['Note'] = $date->getNote()?->getNote();
            $arr['Edition_Name'] = $date->getEdition()->getEditionName();
        }
        $str = '';

        // Special case -- unpublished:
        if ($arr['Year'] < 0) {
            $str = 'Unpublished';
        } else {
            // Add the month if we have one:
            if ($arr['Month'] > 0) {
                // Month will be a number from 1-12; adjust to 0-11 for lookup array:
                $str .= $this->months[$arr['Month'] - 1];
            }

            // Add the day if we have one:
            if ($arr['Day'] > 0) {
                if (!empty($str)) {
                    $str .= ' ';
                }
                $str .= $arr['Day'];
            }

            // Add the year:
            if (!empty($str)) {
                $str .= ', ';
            }
            $str .= $arr['Year'];
        }

        // Add the note, if any:
        $note = $arr['Note'];
        if ($showEdition) {
            $name = ($this->fixTitleHelper)($arr['Edition_Name']);
            $note = empty($note) ? $name : $name . ' - ' . $note;
        }
        if (!empty($note)) {
            $str .= " ({$note})";
        }

        return $str;
    }
}
