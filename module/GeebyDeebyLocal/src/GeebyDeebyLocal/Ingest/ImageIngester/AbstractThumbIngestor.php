<?php

/**
 * Abstract thumbnail loader.
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Ingest\ImageIngester;

use GeebyDeeby\Db\Service\EditionsFullTextService;
use GeebyDeeby\Db\Service\EditionsImageService;
use GeebyDeebyLocal\Ingest\BaseIngester;

use function in_array;

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
    use \GeebyDeebyConsole\ConsoleOutputTrait;

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
     * Note ID to associate with images from this source.
     *
     * @var int
     */
    protected $noteID = null;

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
     * Load missing images.
     *
     * @return void
     */
    public function ingestImages()
    {
        $imageService = $this->getDbService(EditionsImageService::class);
        $existingImages = array_map(
            fn ($row) => $row->getEdition()->getId(),
            $imageService->getByDomain($this->domain)
        );
        $fullTextService = $this->getDbService(EditionsFullTextService::class);
        foreach ($fullTextService->getFullTextForSource($this->fullTextSource) as $link) {
            $linkEditionId = $link->getEdition()->getId();
            if (!in_array($linkEditionId, $existingImages)) {
                $this->writeln('Adding image to edition ' . $linkEditionId);
                try {
                    $iiifUrl = $this->getIIIFURI($link->getUrl());
                } catch (\Exception $e) {
                    // Skip bad images....
                    $this->writeln($e->getMessage());
                    continue;
                }
                $entity = $imageService->createEntity()
                    ->setEdition($link->getEdition())
                    ->setImagePath($link->getUrl())
                    ->setIiifUri($iiifUrl)
                    ->setNote($this->noteID);
                $imageService->persistEntity($entity);
            }
        }
    }

    /**
     * Convert a full-text link to an image URI.
     *
     * @param string $uri Full text link
     *
     * @return string
     */
    abstract protected function getIIIFURI($uri);
}
