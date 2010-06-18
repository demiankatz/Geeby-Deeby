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

/* Script to validate ISBNs in database and backfill ISBN-13's.
 */

require_once 'Gamebooks/GBDB.php';
require_once 'Gamebooks/ISBN.php';

$db = new GBDB();

// Populate ISBN-13's:
$problems = array();
$res = $db->query("SELECT * FROM Items_ISBNs WHERE ISBN IS NOT NULL AND ISBN13 IS NULL");
while ($tmp = $db->fetchAssoc($res)) {
    $i = new ISBN($tmp['ISBN']);
    $i13 = $i->get13();
    if ($i13 !== false) {
        $i13 = $db->escape($i13);
        $seq = intval($tmp['Sequence_ID']);
        $q = "UPDATE Items_ISBNs SET ISBN13='{$i13}' WHERE Sequence_ID='{$seq}'";
        $db->query($q);
    } else {
        $problems[] = $tmp['ISBN'];
    }
}

// Show problems encountered during conversion:
if (empty($problems)) {
    echo '<p>No ISBN problems found.</p>';
} else {
    echo '<p>Problem ISBNs:<br /><br />';
    foreach($problems as $problem) {
        echo $problem . '<br />';
    }
    echo '</p>';
}
?>