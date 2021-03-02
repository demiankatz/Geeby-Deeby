<?php
/**
 * Class to load NIU thumbnails into the database.
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
namespace GeebyDeebyLocal\Ingest\ImageIngester;

/**
 * Class to load NIU thumbnails into the database.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class NIU extends AbstractThumbIngestor
{
    /**
     * Domain for full text links
     *
     * @var string
     */
    protected $domain = 'niu.edu';

    /**
     * Full text source ID for this provider
     *
     * @var int
     */
    protected $fullTextSource = self::FULLTEXT_SOURCE_NIU;

    /**
     * Note ID to associate with images from this source.
     *
     * @var int
     */
    protected $noteID = 102;

    /**
     * PID prefix
     *
     * @var string
     */
    protected $pidPrefix = 'dimenovels';

    /**
     * Solr harvester
     *
     * @var SolrHarvester
     */
    protected $solr;

    /**
     * Constructor
     *
     * @param object        $tables Table plugin manager
     * @param SolrHarvester $solr   Solr harvester
     */
    public function __construct($tables, $solr)
    {
        parent::__construct($tables);
        $this->solr = $solr;
    }

    /**
     * Load missing images.
     *
     * @return void
     */
    public function ingestImages()
    {
        // Wire up output before proceeding...
        $this->solr->setOutputInterface($this->outputInterface);
        return parent::ingestImages();
    }

    /**
     * Convert a full-text link to an image URI.
     *
     * @param string $uri Full text link
     *
     * @return string
     */
    protected function getIIIFURI($uri)
    {
        $pid = $this->solr->getFirstPagePID($this->extractPID($uri));
        if (!$pid) {
            throw new \Exception("Could not find first page PID for $uri");
        }
        return "https://dimenovels.lib.niu.edu/iiif/2/" . urlencode($pid);
    }
}
