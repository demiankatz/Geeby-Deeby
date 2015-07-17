<?php
/**
 * MODS Extractor
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
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Ingest;

/**
 * MODS Extractor
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ModsExtractor
{
    public function getDetails($mods)
    {
        $contents = [];
        foreach ($mods->xpath('/mods:mods') as $current) {
            $currentDetails = $this->extractDetails($current);
            if (!empty($currentDetails)) {
                $contents[] = $currentDetails;
            }
        }
        foreach ($mods->xpath('/mods:mods/mods:relatedItem[@type="constituent"]') as $current) {
            $currentDetails = $this->extractDetails($current);
            if (!empty($currentDetails)) {
                $contents[] = $currentDetails;
            }
        }
        $retVal = compact('contents');
        if ($pub = $this->extractPublisher($mods->xpath('/mods:mods/mods:originInfo[@eventType="publication"]'))) {
            $retVal['publisher'] = $pub;
        }
        $date = $mods->xpath('/mods:mods/mods:originInfo[@eventType="publication"]/mods:dateIssued');
        if (isset($date[0])) {
            $retVal['date'] = (string) $date[0];
        }
        if ($seriesInfo = $this->extractSeries($mods->xpath('/mods:mods/mods:relatedItem[@type="series"]/mods:titleInfo'))) {
            $retVal['series'] = $seriesInfo;
        }
        if ($oclc = $this->extractAll($mods->xpath('/mods:mods/mods:identifier[@type="oclc"]'))) {
            $retVal['oclc'] = $oclc;
        }
        if ($url = $this->extractAll($mods->xpath('/mods:mods/mods:location/mods:url'))) {
            $retVal['url'] = $url;
        }
        return $retVal;
    }

    protected function extractAll($mods)
    {
        $all = [];
        foreach ($mods as $current) {
            if (!empty((string) $current)) {
                $all[] = (string) $current;
            }
        }
        return empty($all) ? false : $all;
    }

    protected function extractSeries($series)
    {
        $seriesInfo = [];
        foreach ($series as $current) {
            $name = $current->xpath('mods:title');
            $number = $current->xpath('mods:partNumber');
            if (isset($name[0]) && !empty((string) $name[0])) {
                $seriesInfo[(string)$name[0]] = isset($number[0]) ? (string) $number[0] : '';
            }
        }
        return empty($seriesInfo) ? false : $seriesInfo;
    }

    protected function extractPublisher($mods)
    {
        if (!isset($mods[0])) {
            return false;
        }
        $mods = $mods[0];
        $pub = [];
        $publisher = $mods->xpath('mods:publisher');
        if (isset($publisher[0])) {
            $pub['name'] = (string) $publisher[0];
            $place = $mods->xpath('mods:place/mods:placeTerm[@type="text"]');
            if (isset($place[0])) {
                $pub['place'] = (string) $place[0];
            }
        }
        return empty($pub) ? false : $pub;
    }
    
    protected function extractDetails($mods)
    {
        $details = [];
        $title = $this->extractTitleInfo($mods);
        if (!empty($title)) {
            $details['title'] = $title;
        }
        $authors = $this->extractAuthors($mods);
        if (!empty($authors)) {
            $details['authors'] = $authors;
        }
        $subjects = $this->extractSubjects($mods);
        if (!empty($subjects)) {
            $details['subjects'] = $subjects;
        }
        return $details;
    }

    protected function extractAuthors($mods)
    {
        $authors = [];
        $matches = $mods->xpath('mods:name');
        foreach ($matches as $current) {
            $role = $current->xpath('mods:role/mods:roleTerm');
            if (isset($role[0]) && (string)$role[0] == 'author') {
                $currentAuthor = [];
                $uri = $current->xpath('@valueURI');
                if (isset($uri[0])) {
                    $currentAuthor['uri'] = (string)$uri[0];
                }
                $currentAuthor['name'] = implode(', ', $current->xpath('mods:namePart'));
                if (!empty($currentAuthor['name'])) {
                    $authors[] = $currentAuthor;
                }
            }
        }
        return $authors;
    }

    protected function extractSubjects ($mods)
    {
        $results = [];
        $paths = [
            'mods:genre', 'mods:topic', 'mods:geographic', 'mods:temporal',
            'mods:subject/mods:name' // don't grab just any name
        ];
        $prefixedPaths = [];
        foreach ($paths as $path) {
            $matches = $mods->xpath($path . '|mods:subject/' . $path);
            foreach ($matches as $current) {
                $uri = $current->xpath('@valueURI');
                if (isset($uri[0])) {
                    $results[(string)$uri[0]] = (string)$current;
                }
            }
        }
        return $results;
    }

    protected function extractTitleInfo($mods)
    {
        $matches = $mods->xpath('mods:titleInfo[not(@type="alternative")]');
        if (empty($matches)) {
            return '';
        }
        $title = (string)$matches[0]->xpath('mods:title')[0];
        $article = $matches[0]->xpath('mods:nonSort');
        $full = $title .= (empty($article) ? '' : ', ' . (string)$article[0]);
        return $full;
    }
}
