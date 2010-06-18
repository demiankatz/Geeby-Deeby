<?php
/**
  *
  * Copyright (c) Demian Katz 2010.
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
  */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.formatReleaseDate.php
 * Type:     modifier
 * Name:     formatReleaseDate
 * Purpose:  Display a release date based on a row from the
 *           Items_Release_Dates table.
 * -------------------------------------------------------------
 */
function smarty_modifier_formatReleaseDate($arr)
{
    static $months = array('January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December');
    $str = '';
    
    // Special case -- unpublished:
    if ($arr['Year'] < 0) {
        $str = 'Unpublished';
    } else {
        // Add the month if we have one:
        if ($arr['Month'] > 0) {
            // Month will be a number from 1-12; adjust to 0-11 for lookup array:
            $str .= $months[$arr['Month'] - 1];
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
    if (!empty($arr['Note'])) {
        $str .= " ({$arr['Note']})";
    }
    
    return $str;
}
?>