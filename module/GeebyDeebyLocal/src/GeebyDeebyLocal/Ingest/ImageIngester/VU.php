<?php

/**
 * Class to load Villanova thumbnails into the database.
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

/**
 * Class to load Villanova thumbnails into the database.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class VU extends AbstractThumbIngestor
{
    /**
     * Domain for full text links
     *
     * @var string
     */
    protected $domain = 'villanova.edu';

    /**
     * Full text source ID for this provider
     *
     * @var int
     */
    protected $fullTextSource = self::FULLTEXT_SOURCE_VU;

    /**
     * Note ID to associate with images from this source.
     *
     * @var int
     */
    protected $noteID = 103;

    /**
     * PID prefix
     *
     * @var string
     */
    protected $pidPrefix = 'vudl';

    /**
     * Convert a full-text link to an image URI.
     *
     * @param string $uri Full text link
     *
     * @return string
     */
    protected function getIIIFURI($uri)
    {
        $manifest = json_decode(file_get_contents("$uri/Manifest"));
        $image = $manifest->sequences[0]->canvases[0]->images[0]
            ->resource->service->{'@id'} ?? null;
        if (null === $image) {
            throw new \Exception(
                "Problem finding IIIF source for " . $this->extractPID($uri)
            );
        }
        return $image;
    }
}
