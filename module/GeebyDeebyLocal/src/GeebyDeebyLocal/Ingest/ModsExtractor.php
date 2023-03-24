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
    /**
     * Subject URIs that should be ignored during extraction.
     *
     * @var string[]
     */
    protected $subjectUrisToIgnore = [
        'http://vocab.getty.edu/aat/300028051', // 'books' -- not very meaningful
    ];

    /**
     * Given a simple XML object, extract details.
     *
     * @param object $mods Simple XML object representing a MODS record.
     *
     * @return array
     */
    public function getDetails($mods)
    {
        $contents = [];
        foreach ($mods->xpath('/mods:mods') as $current) {
            $currentDetails = $this->extractDetails($current);
            if (!empty($currentDetails)) {
                $contents[] = $currentDetails;
            }
        }
        $constituentPath = '/mods:mods/mods:relatedItem[@type="constituent"]';
        foreach ($mods->xpath($constituentPath) as $current) {
            $currentDetails = $this->extractDetails($current);
            if (!empty($currentDetails)) {
                $contents[] = $currentDetails;
            }
        }
        $retVal = compact('contents');
        $pub = $this->extractPublisher(
            $mods->xpath('/mods:mods/mods:originInfo[@eventType="publication"]'),
            $mods->xpath(
                '/mods:mods/mods:name[mods:role/mods:roleTerm=\'publisher\']'
                . '/mods:namePart'
            )
        );
        if ($pub) {
            $retVal['publisher'] = $pub;
        }
        $date = $mods->xpath(
            '/mods:mods/mods:originInfo[@eventType="publication"]/mods:dateIssued'
        );
        if (isset($date[0])) {
            $retVal['date'] = (string)$date[0];
        }
        $seriesPath = '/mods:mods/mods:relatedItem[@type="series"]'
            . '/mods:titleInfo[@type="uniform"]';
        if ($seriesInfo = $this->extractSeries($mods->xpath($seriesPath))) {
            $retVal['series'] = $seriesInfo;
        }
        $notes = $this->extractNotes($mods);
        if (!empty($notes)) {
            $retVal['notes'] = $notes;
        }
        $oclcPath = '/mods:mods/mods:identifier[@type="oclc"]';
        if ($oclc = $this->extractAll($mods->xpath($oclcPath))) {
            $retVal['oclc'] = $oclc;
        }
        $urlPath = '/mods:mods/mods:location/mods:url';
        if ($url = $this->extractAll($mods->xpath($urlPath))) {
            $retVal['url'] = $url;
        }
        return $retVal;
    }

    /**
     * Extract all strings from the provided XML object.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractAll($mods)
    {
        $all = [];
        foreach ($mods as $current) {
            $current = (string)$current;
            if (!empty($current)) {
                $all[] = $current;
            }
        }
        return empty($all) ? false : $all;
    }

    /**
     * Extract series information from the provided XML object.
     *
     * @param object $series Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractSeries($series)
    {
        $seriesInfo = [];
        foreach ($series as $current) {
            $name = $current->xpath('mods:title');
            $number = $current->xpath('mods:partNumber');
            $firstName = isset($name[0]) ? (string)$name[0] : null;
            if (!empty($firstName)) {
                // If the same series name has multiple numbers, favor the lowest
                // non-zero value:
                $numStr = (string)($number[0] ?? '');
                if (
                    !isset($seriesInfo[$firstName])
                    || ($numStr > 0 && intval($seriesInfo[$firstName]) < $numStr)
                ) {
                    $seriesInfo[$firstName] = $numStr;
                }
            }
        }
        return empty($seriesInfo) ? false : $seriesInfo;
    }

    /**
     * Extract publisher information.
     *
     * @param object $mods     Simple XML object representing part of a MODS record.
     * @param object $authMods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractPublisher($mods, $authMods = [])
    {
        if (!isset($mods[0]) && !isset($authMods[0])) {
            return false;
        }
        $pub = [];
        if (isset($mods[0])) {
            $mods = $mods[0];
            $publisher = $mods->xpath('mods:publisher');
            if (isset($publisher[0])) {
                $pub['name'] = (string)$publisher[0];
                $place = $mods->xpath('mods:place/mods:placeTerm[@type="text"]');
                if (isset($place[0])) {
                    $pub['place'] = (string)$place[0];
                }
            }
        }
        // If we have an authorized name with a date in it, or if the authorized name
        // is longer than the imprint name, override the one extracted above:
        $parts = explode(',', $pub['name'] ?? '');
        $authModsStr = (string)($authMods[0] ?? '');
        if (
            preg_match('/\(\d{4}/', $authModsStr)
            || strlen($authModsStr) > strlen($parts[0])
        ) {
            $newParts = explode(',', $authModsStr);
            foreach ($newParts as $i => $part) {
                $parts[$i] = $part;
            }
            $pub['name'] = implode(',', $parts);
        }
        return empty($pub) ? false : $pub;
    }

    /**
     * Extract key details from the MODS.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractDetails($mods)
    {
        $details = [];
        $title = $this->extractTitleInfo($mods);
        if (!empty($title)) {
            $details['title'] = $title;
        }
        $altTitles = $this->extractAltTitleInfo($mods);
        if (!empty($altTitles)) {
            $details['altTitles'] = $altTitles;
        }
        $authors = $this->extractPeople($mods, 'author');
        if (!empty($authors)) {
            $details['authors'] = $authors;
        }
        $editors = $this->extractPeople($mods, 'editor');
        if (!empty($editors)) {
            $details['editors'] = $editors;
        }
        $extent = $this->extractExtent($mods);
        if (!empty($extent)) {
            $details['extent'] = $extent;
        }
        $subjects = $this->extractSubjects($mods);
        if (!empty($subjects)) {
            $details['subjects'] = $subjects;
        }
        return $details;
    }

    /**
     * Given the result of an XPath to retrieve name parts, construct a string.
     *
     * @param array  $parts    SimpleXML XPath results.
     * @param string $nameType The type of name being extracted.
     *
     * @return string
     */
    protected function getStringFromNameParts($parts, $nameType)
    {
        $currentName = '';
        foreach ($parts as $namePart) {
            $type = $namePart->xpath('@type');
            $namePartStr = (string)$namePart;
            if (strlen($currentName) > 0) {
                $type = isset($type[0]) ? (string)$type[0] : 'default';
                switch ($type) {
                    case 'date':
                        $currentName .= ', ';
                        break;
                    case 'termsOfAddress':
                        $currentName .= (substr($namePartStr, 0, 1) == '(')
                            ? ' ' : ', ';
                        break;
                    default:
                        $currentName .= $nameType == 'corporate' ? ' -- ' : ' ';
                        break;
                }
            }
            $currentName .= $namePartStr;
        }
        return trim($currentName);
    }

    /**
     * Extract person information from the MODS.
     *
     * @param object $mods        Simple XML object representing part of a MODS
     * record.
     * @param string $desiredRole Role to extract
     *
     * @return array
     */
    protected function extractPeople($mods, $desiredRole = 'author')
    {
        $people = [];
        $matches = $mods->xpath('mods:name');
        foreach ($matches as $current) {
            $nameType = $current->xpath('@type');
            $nameType = isset($nameType[0]) ? (string)$nameType[0] : 'default';
            $role = $current->xpath('mods:role/mods:roleTerm');
            if (isset($role[0]) && (string)$role[0] == $desiredRole) {
                $currentPerson = [];
                $uri = $current->xpath('@valueURI');
                if (isset($uri[0])) {
                    $currentPerson['uri'] = (string)$uri[0];
                }
                $currentPerson['name'] = $this->getStringFromNameParts(
                    $current->xpath('mods:namePart'),
                    $nameType
                );
                if (!empty($currentPerson['name'])) {
                    $people[] = $currentPerson;
                }
            }
        }
        return $people;
    }

    /**
     * Extract extent information from the MODS.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return string
     */
    protected function extractExtent($mods)
    {
        $part = $mods
            ->xpath('mods:part/mods:detail[@type="part"]/mods:number');
        $part = isset($part[0]) ? (string)$part[0] : '';
        $chapter = $mods
            ->xpath('mods:part/mods:detail[@type="chapter"]/mods:number');
        $chapter = isset($chapter[0]) ? (string)$chapter[0] : '';
        $pageStart = $mods->xpath('mods:part/mods:extent[@unit="pages"]/mods:start');
        $pageStart = isset($pageStart[0]) ? (string)$pageStart[0] : '';
        $pageEnd = $mods->xpath('mods:part/mods:extent[@unit="pages"]/mods:end');
        $pageEnd = isset($pageEnd[0]) ? (string)$pageEnd[0] : $pageStart;
        $pageRange = ($pageStart === $pageEnd)
            ? (empty($pageStart) ? '' : 'page ' . $pageStart)
            : (empty($pageEnd) ? '' : "pages $pageStart-$pageEnd");
        $parts = [];
        if (!empty($part)) {
            $parts[] = "part $part";
        }
        if (!empty($chapter)) {
            $parts[] = (strstr($chapter, '-') ? 'chapters ' : 'chapter ') . $chapter;
        }
        if (!empty($pageRange)) {
            $parts[] = $pageRange;
        }
        return implode(', ', $parts);
    }

    /**
     * Extract subject information from the MODS.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractSubjects($mods)
    {
        $results = [];
        $paths = [
            'mods:genre', 'mods:topic', 'mods:geographic', 'mods:temporal',
            'mods:subject/mods:name', // don't grab just any name
        ];
        $prefixedPaths = [];
        foreach ($paths as $path) {
            $matches = $mods->xpath($path . '|mods:subject/' . $path);
            foreach ($matches as $current) {
                $uri = $current->xpath('@valueURI');
                $value = trim((string)$current);
                if (empty($value)) {
                    $nameType = $current->xpath('@type');
                    $nameType = isset($nameType[0])
                        ? (string)$nameType[0] : 'default';
                    $value = $this->getStringFromNameParts(
                        $current->xpath('mods:namePart'),
                        $nameType
                    );
                }
                if (
                    isset($uri[0])
                    && !in_array($uri[0], $this->subjectUrisToIgnore)
                ) {
                    $results[(string)$uri[0]] = trim($value);
                }
            }
        }
        return $results;
    }

    /**
     * Extract note information from the MODS.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractNotes($mods)
    {
        $results = [];
        $paths = [
            'mods:note',
        ];
        $regEx = '/^Includes (short|article|department)/';
        $prefixedPaths = [];
        foreach ($paths as $path) {
            $matches = $mods->xpath($path . '|mods:subject/' . $path);
            foreach ($matches as $current) {
                $value = trim((string)$current);
                if (preg_match($regEx, $value)) {
                    $results[] = $value;
                }
            }
        }
        return $results;
    }

    /**
     * Convert a title element from MODS into a string
     *
     * @param object $current         Simple XML object representing part of a MODS
     * record.
     * @param bool   $includeSubtitle Should we include the subtitle?
     *
     * @return string
     */
    protected function assembleTitle($current, $includeSubtitle = true)
    {
        $title = trim((string)$current->xpath('mods:title')[0]);
        if ($includeSubtitle) {
            $subTitleParts = $current->xpath('mods:subTitle');
            $subtitle = isset($subTitleParts[0])
                ? trim((string)$subTitleParts[0]) : '';
            if (!empty($subtitle)) {
                $title .= ' : ' . $subtitle;
            }
        }
        $partParts = $current->xpath('mods:partNumber');
        $part = isset($partParts[0]) ? trim((string)$partParts[0]) : '';
        if (!empty($part)) {
            $title .= ', ' . $part;
        }
        $partNameParts = $current->xpath('mods:partName');
        $partName = isset($partNameParts[0]) ? trim((string)$partNameParts[0]) : '';
        if (!empty($partName)) {
            $title .= ' : ' . $partName;
        }
        $article = $current->xpath('mods:nonSort');
        return $title . (empty($article) ? '' : ', ' . trim((string)$article[0]));
    }

    /**
     * Extract title information from the MODS.
     *
     * @param object $mods            Simple XML object representing part of a MODS
     * record.
     * @param bool   $includeSubtitle Should we include the subtitle?
     *
     * @return string
     */
    protected function extractTitleInfo($mods, $includeSubtitle = false)
    {
        $matches = $mods->xpath(
            'mods:titleInfo[not(@type="alternative") and not(@type="uniform")]'
        );
        if (empty($matches)) {
            return '';
        }
        return $this->assembleTitle($matches[0], $includeSubtitle);
    }

    /**
     * Extract alt-title information from the MODS.
     *
     * @param object $mods Simple XML object representing part of a MODS record.
     *
     * @return array
     */
    protected function extractAltTitleInfo($mods)
    {
        $matches = $mods->xpath('mods:titleInfo[@type="alternative"]');
        $results = array_map([$this, 'assembleTitle'], $matches);
        $mainTitleNoSub = $this->extractTitleInfo($mods, false);
        $mainTitleWithSub = $this->extractTitleInfo($mods, true);
        if ($mainTitleNoSub != $mainTitleWithSub) {
            $results[] = $mainTitleWithSub;
        }
        return $results;
    }
}
