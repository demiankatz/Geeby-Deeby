<?php

/**
 * Solr Harvester
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

/**
 * Solr Harvester
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SolrHarvester
{
    use \GeebyDeebyConsole\ConsoleOutputTrait;

    /**
     * Settings
     *
     * @var object
     */
    protected $settings;

    /**
     * Should we use a cache?
     *
     * @var bool
     */
    protected $cache = false;

    /**
     * Constructor
     *
     * @param object $settings Settings
     */
    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Get the settings
     *
     * @return object
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Retrieve from Solr all records matching existing editions on dimenovels.org.
     *
     * @return array Edition IDs
     */
    public function getExistingEditions()
    {
        $query = $this->settings->solrQueryField . ':"https://dimenovels.org/*"';
        $field = $this->settings->solrQueryField;
        $solr = $this->querySolr($query, $field);
        $editions = [];
        foreach ($solr->response->docs as $current) {
            $parts = explode('/', $current->{$this->settings->solrQueryField});
            $currentEd = array_pop($parts);
            $editions[] = $currentEd;
        }
        return $editions;
    }

    /**
     * Given the PID of an item, get the PID of its first page (false if not
     * found).
     *
     * @param string $pid PID
     *
     * @return string|bool
     */
    public function getFirstPagePID($pid)
    {
        $query = 'RELS_EXT_isPageOf_uri_ms:"info:fedora/' . $pid
            . '" AND RELS_EXT_isSequenceNumber_literal_ms:"1"';
        $field = $this->settings->solrIdField;
        $solr = $this->querySolr($query, $field);
        return $solr->response->docs[0]->$field ?? false;
    }

    /**
     * Given the PID of an item, get PIDs of all of its pages.
     *
     * @param string $pid PID
     *
     * @return array
     */
    public function getAllPagePIDs($pid)
    {
        $query = 'RELS_EXT_isPageOf_uri_ms:"info:fedora/' . $pid . '"';
        $field = $this->settings->solrIdField
            . ',RELS_EXT_isSequenceNumber_literal_ms';
        $solr = $this->querySolr($query, $field);
        $results = [];
        if (isset($solr->response->docs)) {
            foreach ($solr->response->docs as $doc) {
                $results[$doc->RELS_EXT_isSequenceNumber_literal_ms[0]]
                    = $doc->{$this->settings->solrIdField};
            }
        }
        return $results;
    }

    /**
     * Given an edition, retrieve a PID. Return false if no match found.
     *
     * @param string $edition Edition
     *
     * @return string|bool
     */
    public function getPidForEdition($edition)
    {
        // Get MODS identifier from Solr:
        $query = $this->settings->solrQueryField . ':"' . $edition . '"';
        $field = $this->settings->solrIdField;
        $solr = $this->querySolr($query, $field);
        return $solr->response->docs[0]->$field ?? false;
    }

    /**
     * Retrieve Fedora PIDs matching a given series name.
     *
     * @param string $series Series to retrieve.
     *
     * @return array Fedora PIDs.
     */
    public function getSeriesEntries($series)
    {
        $query = $this->settings->solrSeriesField
            . ':"' . addcslashes($series, '"') . '"';
        $field = $this->settings->solrIdField;
        $sort = $this->settings->solrSeriesSortField ?? null;
        $solr = $this->querySolr($query, $field, $sort);
        $retVal = [];
        foreach ($solr->response->docs as $doc) {
            $pid = $doc->$field ?? false;
            if ($pid) {
                $retVal[] = $pid;
            }
        }
        return $retVal;
    }

    /**
     * Retrieve Fedora PIDs matching a given collection name.
     *
     * @param string $collection Collection to retrieve.
     *
     * @return array Fedora PIDs.
     */
    public function getCollectionEntries($collection)
    {
        $query = $this->settings->solrCollectionField
            . ':"' . addcslashes($collection, '"') . '"';
        $field = $this->settings->solrIdField;
        $sort = $this->settings->solrCollectionSortField ?? null;
        $solr = $this->querySolr($query, $field, $sort);
        $retVal = [];
        foreach ($solr->response->docs as $doc) {
            $pid = $doc->$field ?? false;
            if ($pid) {
                $retVal[] = $pid;
            }
        }
        return $retVal;
    }

    /**
     * Perform a Solr query.
     *
     * @param string $query Query to execute
     * @param string $fl    Field list to retrieve
     * @param string $sort  Sort option
     *
     * @return object
     */
    protected function querySolr($query, $fl, $sort = null)
    {
        $url = (string)$this->settings->solrUrl
            . '?q=' . urlencode($query) . '&wt=json'
            . '&rows=10000&fl=' . urlencode($fl);
        if ($sort) {
            $url .= '&sort=' . urlencode($sort);
        }
        $cache = $this->cache ? '/tmp/gbdb_' . md5("$query-$fl") : false;
        if (!$cache || !file_exists($cache)) {
            $this->writeln("Querying {$this->settings->solrUrl} for $query...");
            $solrResponse = file_get_contents($url);
            if ($cache) {
                file_put_contents($cache, $solrResponse);
            }
        } else {
            $solrResponse = file_get_contents($cache);
        }
        return json_decode($solrResponse);
    }
}
