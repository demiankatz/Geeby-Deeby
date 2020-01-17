<?php
/**
 * Fedora Harvester
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
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Ingest;

use Zend\Console\Console;

/**
 * Fedora Harvester
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FedoraHarvester
{
    /**
     * Base URL for retrieving MODS.
     *
     * @var string
     */
    protected $modsUrl;

    /**
     * Solr harvester.
     *
     * @var SolrHarvester
     */
    protected $solr;

    /**
     * Should we use a cache?
     *
     * @var bool
     */
    protected $cache = false;

    /**
     * Constructor
     *
     * @param string        $modsUrl Base URL for retrieving MODS.
     * @param SolrHarvester $solr    Solr Harvester
     */
    public function __construct($modsUrl, SolrHarvester $solr)
    {
        $this->modsUrl = $modsUrl;
        $this->solr = $solr;
    }

    /**
     * Retrieve the MODS record for a given PID. Return false if no record can be
     * retrieved.
     *
     * @param string|bool $pid PID (if false, no record retrieved)
     *
     * @return string|bool
     */
    public function getModsForPid($pid)
    {
        if (!$pid) {
            return false;
        }

        // Retrieve MODS from repository:
        $cache = $this->cache ? '/tmp/gbdb_pid_' . md5($pid) : false;
        if ($cache && file_exists($cache)) {
            return file_get_contents($cache);
        }
        $modsUrl = sprintf($this->modsUrl, $pid);
        Console::writeLine("Retrieving $modsUrl...");
        $mods = file_get_contents($modsUrl);
        if ($cache && $mods) {
            file_put_contents($cache, $mods);
        }
        return $mods;
    }

    /**
     * Retrieve the MODS record for a given edition. Return false if no record can
     * be retrieved.
     *
     * @param string $edition Edition to retrieve.
     *
     * @return string|bool
     */
    public function getModsForEdition($edition)
    {
        return $this->getModsForPid($this->solr->getPidForEdition($edition));
    }
}
