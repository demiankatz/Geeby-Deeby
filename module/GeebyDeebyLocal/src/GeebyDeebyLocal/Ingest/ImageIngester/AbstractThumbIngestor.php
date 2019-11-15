<?php
/**
 * Abstract thumbnail loader.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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

use GeebyDeebyLocal\Ingest\BaseIngester;
use Zend\Console\Console;

/**
 * Abstract thumbnail loader.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
abstract class AbstractThumbIngestor extends BaseIngester
{
    /**
     * Domain for full text links
     *
     * @var string
     */
    protected $domain;

    /**
     * Full text source ID for this provider
     *
     * @var int
     */
    protected $fullTextSource;

    /**
     * PID prefix
     *
     * @var string
     */
    protected $pidPrefix;

    /**
     * Extract a PID from a URI.
     *
     * @param string $uri URI
     *
     * @return string|bool
     */
    protected function extractPID($uri)
    {
        $regex = '/' . $this->pidPrefix . ':[0-9]+/';
        preg_match($regex, str_replace('%3A', ':', $uri), $matches);
        return empty($matches[0]) ? false : $matches[0];
    }


    /**
     * Load missing NIU images.
     */
    public function ingestImages()
    {
        $existingImages = $this->getExistingImages();
        $table = $this->getDbTable('editionsimages');
        foreach ($this->getMissingImageLinks() as $link) {
            if (!in_array($link->Edition_ID, $existingImages)) {
                Console::writeLine("Adding image to edition " . $link->Edition_ID);
                try {
                    $iiifUrl = $this->getIIIFURI($link->Full_Text_URL);
                } catch (\Exception $e) {
                    // Skip bad images....
                    Console::writeLine($e->getMessage());
                    continue;
                }
                $table->insert(
                    [
                        'Edition_ID' => $link->Edition_ID,
                        'Image_Path' => $link->Full_Text_URL,
                        'IIIF_URI' => $iiifUrl,
                    ]
                );
            }
        }
    }

    /**
     * Get a list of Edition_ID values that already have images.
     *
     * @return array
     */
    protected function getExistingImages()
    {
        $callback = function ($select) {
            $select->where->like('Image_Path', '%' . $this->domain . '%');
        };
        $results = [];
        foreach ($this->getDbTable('editionsimages')->select($callback) as $current) {
            $results[] = $current->Edition_ID;
        }
        return $results;
    }

    /**
     * Convert a full-text link to an image URI.
     *
     * @param string $uri Full text link
     *
     * @return string
     */
    abstract protected function getIIIFURI($uri);

    /**
     * Get a list of full text links that lack corresponding images.
     *
     * @return \Iterable
     */
    protected function getMissingImageLinks()
    {
        $callback = function ($select) {
            $select->where(['Full_Text_Source_ID' => $this->fullTextSource]);
        };
        return $this->getDbTable('editionsfulltext')->select($callback);
    }
}
