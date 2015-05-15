<?php
/**
 * Ingest controller
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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;
use Zend\Console\Console;

/**
 * Ingest controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IngestController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Standard ingest action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $mods = $this->getModsForEdition('http://dimenovels.org/Edition/5865');
        if (!$mods) {
            Console::writeLine('Could not retrieve MODS.');
            return;
        }
    }

    protected function getModsForEdition($edition)
    {
        // Get MODS identifier from Solr:
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'));
        $query = $settings->solrQueryField . ':"' . $edition . '"';
        $field = $settings->solrIdField;
        $url = (string)$settings->solrUrl . '?q=' . urlencode($query) . '&wt=json'
            . '&fl=' . urlencode($field);
        Console::writeLine("Querying {$settings->solrUrl} for $query...");
        $solr = json_decode(file_get_contents($url));
        $pid = isset($solr->response->docs[0]->PID)
            ? $solr->response->docs[0]->PID : false;
        if (!$pid) {
            return false;
        }

        // Retrieve MODS from repository:
        $modsUrl = sprintf($settings->modsUrl, $pid);
        Console::writeLine("Retrieving $modsUrl...");
        $mods = file_get_contents($modsUrl);
        return $mods;
    }
}
