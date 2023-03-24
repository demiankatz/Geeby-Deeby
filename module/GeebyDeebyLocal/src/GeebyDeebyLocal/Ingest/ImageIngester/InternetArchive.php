<?php

/**
 * Class to load Internet Archive thumbnails into the database.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2021.
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
 * Class to load Internet Archive thumbnails into the database.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class InternetArchive extends AbstractThumbIngestor
{
    /**
     * Domain for full text links
     *
     * @var string
     */
    protected $domain = 'archive.org';

    /**
     * Full text source ID for this provider
     *
     * @var int
     */
    protected $fullTextSource = self::FULLTEXT_SOURCE_IA;

    /**
     * Note ID to associate with images from this source.
     *
     * @var int
     */
    protected $noteID = 105;

    /**
     * Convert a full-text link to an image URI.
     *
     * @param string $uri Full text link
     *
     * @return string
     */
    protected function getIIIFURI($uri)
    {
        $parts = explode('/', rtrim($uri, '/'));
        $id = array_pop($parts);
        $url = "https://iiif.archivelab.org/iiif/$id/manifest.json";
        $manifest = json_decode(file_get_contents($url));
        // IA manifests include a non-standard "cover" element which may
        // point at a IIIF-capable cover image. We'll fall back on the first
        // image if the cover is missing.
        $image = $manifest->cover
            ?? $manifest->sequences[0]->canvases[0]->images[0]
            ->resource->service->{'@id'} ?? null;
        if (null === $image) {
            throw new \Exception(
                "Problem finding IIIF source for " . $uri
            );
        }
        return $image;
    }
}
