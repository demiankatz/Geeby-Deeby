<?php
/**
 * Class to load information into the database.
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
use Zend\Console\Prompt;

/**
 * Class to load information into the database.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class DatabaseIngester extends BaseIngester
{
    /**
     * Articles helper
     *
     * @var object
     */
    protected $articles;

    /**
     * Constructor
     *
     * @param object $tables Table plugin manager
     */
    public function __construct($tables, $articles)
    {
        parent::__construct($tables);
        $this->articles = $articles;
    }

    /**
     * Ingest data.
     *
     * @param array  $details Details from ModsExtractor or equivalent
     * @param string $type    Type of import (existing or series)
     * @param object $extra   Support object (Edition row for existing, Series row for series)
     *
     * @return bool True for success
     */
    public function ingest($details, $type, $extra = null)
    {
        if ($type == 'existing') {
            return $this->ingestExisting($details, $extra);
        }
        return $this->ingestSeries($details, $extra);
    }

    /**
     * Ingest existing Issue edition
     *
     * @param array  $details    Details from ModsExtractor or equivalent
     * @param object $editionObj Edition row
     * @param array  $series     Series summary array (see getSeriesForEdition)
     *
     * @return bool True for success
     */
    protected function ingestExistingIssue($details, $editionObj, $series)
    {
        $childDetails = $this->synchronizeChildren($editionObj, $details['contents']);
        if (!$childDetails) {
            return false;
        }
        $childDetails = $this->addAuthorDetails($childDetails);
        if (!$childDetails) {
            return false;
        }
        $details['contents'] = $childDetails;
        return $this->updateDatabaseForHierarchicalEdition($editionObj, $series, $details);
    }

    /**
     * Ingest existing Work edition
     *
     * @param array  $details    Details from ModsExtractor or equivalent
     * @param object $editionObj Edition row
     * @param array  $item       Item summary array (see getItemForEdition)
     * @param array  $series     Series summary array (see getSeriesForEdition)
     *
     * @return bool True for success
     */
     protected function ingestExistingWork($details, $editionObj, $item, $series)
     {
        if (count($details['contents']) != 1) {
            Console::writeLine("FATAL: too many contents for single-part item.");
            return false;
        }
        if (!$this->checkItemTitles($item, $details['contents'][0])) {
            Console::writeLine("FATAL: Title mismatch '{$details['contents'][0]['title']}' vs. '{$item['Item_Name']}' for item {$item['Item_ID']}.");
            return false;
        }
        $db = ['item' => $item, 'edition' => $editionObj->toArray()];
        $childDetails = [[$details['contents'][0], $db]];
        if (!($childDetails = $this->addAuthorDetails($childDetails))) {
            return false;
        }
        if (!$this->updateWorkInDatabase($childDetails[0][0], $childDetails[0][1])) {
            return false;
        }
        return $this->setTopLevelDetails($editionObj, $series, $details);
    }

    /**
     * Ingest existing edition
     *
     * @param array  $details    Details from ModsExtractor or equivalent
     * @param object $editionObj Edition row
     *
     * @return bool True for success
     */
    protected function ingestExisting($details, $editionObj)
    {
        $series = $this->getSeriesForEdition($editionObj);
        $item = $this->getItemForEdition($editionObj);

        if (!$this->validateSeries($details, $editionObj, $series)) {
            Console::writeLine('Series validation failed.');
            return false;
        }

        if ($item['Material_Type_ID'] == self::MATERIALTYPE_ISSUE) {
            return $this->ingestExistingIssue($details, $editionObj, $series);
        } else if ($item['Material_Type_ID'] == self::MATERIALTYPE_WORK) {
            return $this->ingestExistingWork($details, $editionObj, $item, $series);
        }

        // If we got this far, we have bad data:
        Console::writeLine("FATAL: unexpected material type ID {$item['Material_Type_ID']}.");
        return false;
    }

    protected function getPositionFromSeriesString($str)
    {
        // Find the last number in the string.
        preg_match_all('/[0-9]+[,\s]*[0-9]*/', $str, $matches);
        $index = count($matches[0]) - 1;
        return isset($matches[0][$index])
            ? preg_replace('/[^0-9]/', '', $matches[0][$index]) : '0';
    }

    /**
     * Ingest series entry
     *
     * @param array  $details   Details from ModsExtractor or equivalent
     * @param object $seriesObj Series row
     *
     * @return bool True for success
     */
    protected function ingestSeries($details, $seriesObj)
    {
        $pos = $this->getPositionFromSeriesString(current($details['series']));
        Console::writeLine("Working on " . $seriesObj->Series_Name . " no. $pos...");
        $childDetails = $this->synchronizeSeriesEntries($seriesObj, $pos, $details['contents']);
        if (!$childDetails) {
            return false;
        }
        $childDetails = $this->addAuthorDetails($childDetails);
        if (!$childDetails) {
            return false;
        }
        $details['contents'] = $childDetails;
        // Special case: multi-part work:
        if (count($childDetails) > 1) {
            $container = trim(current(array_keys($details['series']))) . ' ' . trim(current($details['series']));
            $newEdition = $this->getChildIssueForSeries($seriesObj, ['title' => $container], $pos);
            if (!$newEdition || !$this->setTopLevelDetails($newEdition, $seriesObj, $details)) {
                return false;
            };
            return $this->updateChildWorks($newEdition, $childDetails);
        }
        // Standard case: single-part work:
        return $this->updateDatabaseForFlatEdition($seriesObj, $pos, $details);
    }

    /**
     * Set top-level details (publisher, ID number, links, dates, etc.) for container
     * item.
     *
     * @param object       $editionObj Edition row
     * @param array|object $series     Series summary array (see getSeriesForEdition)
     * or Series row object
     * @param array        $details    Details from ModsExtractor or equivalent
     *
     * @return bool True for success
     */
    protected function setTopLevelDetails($editionObj, $series, $details)
    {
        $item = $this->getItemForEdition($editionObj);
        if (isset($details['date'])) {
            if (!$this->processDate($details['date'], $editionObj)) {
                return false;
            }
        }
        if (isset($details['publisher'])) {
            if (!$this->processPublisher($details['publisher'], $editionObj, $series['Series_ID'])) {
                return false;
            }
        }
        if (isset($details['oclc'])) {
            if (!$this->processOclcNum($details['oclc'], $editionObj)) {
                return false;
            }
        }
        if (isset($details['url'])) {
            if (!$this->processUrl($details['url'], $editionObj)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Write a flat (Work-only) edition into the database.
     *
     * @param object $series  Series row
     * @param int    $pos     Position of new edition in series.
     * @param array  $details Details from ModsExtractor or equivalent, processed
     * through synchronizeSeriesEntries() and addAuthorDetails().
     *
     * @return bool True for success
     */
    protected function updateDatabaseForFlatEdition($series, $pos, $details)
    {
        list($data, $db) = $details['contents'][0];
        if (!$db) {
            if (!($editionObj = $this->addChildWorkToSeries($series, $data, $pos))) {
                return false;
            }
        } else {
            if (!($editionObj = $this->updateWorkInDatabase($data, $db))) {
                return false;
            }
        }
        return $this->setTopLevelDetails($editionObj, $series, $details);
    }

    /**
     * Write a hierarchical (Issue+Works) edition into the database.
     *
     * @param object $editionObj Edition row
     * @param array  $series     Series summary array (see getSeriesForEdition)
     * @param array  $details    Details from ModsExtractor or equivalent, processed
     * through synchronizeChildren() and addAuthorDetails().
     *
     * @return bool True for success
     */
    protected function updateDatabaseForHierarchicalEdition($editionObj, $series, $details)
    {
        if (!$this->setTopLevelDetails($editionObj, $series, $details)) {
            return false;
        }
        return $this->updateChildWorks($editionObj, $details['contents']);
    }

    /**
     * Attempt to parse year, month and day out of arbitrary date string.
     *
     * @param string $date Raw date string
     *
     * @return array of year, month, day
     */
    protected function parseDate($date)
    {
        $parts = preg_split('|[-/]|', str_replace(['[', ']', '?'], '', $date));
        if (isset($parts[2]) && $parts[2] > 50) {
            $year = isset($parts[2]) ? $parts[2] : null;
            $month = isset($parts[0]) ? $parts[0] : null;
            $day = isset($parts[1]) ? $parts[1] : null;
        } else {
            $year = isset($parts[0]) ? $parts[0] : null;
            $month = isset($parts[1]) ? $parts[1] : null;
            $day = isset($parts[2]) ? $parts[2] : null;
        }
        return [$year, $month, $day];
    }
    /**
     * Given a date, update the edition.
     *
     * @param string $date       Date string.
     * @param object $editionObj Row representing Edition row in database.
     *
     * @return bool Success?
     */
    protected function processDate($date, $editionObj)
    {
        list($year, $month, $day) = $this->parseDate($date);
        $table = $this->getDbTable('editionsreleasedates');
        $known = $table->getDatesForEdition($editionObj->Edition_ID);
        $foundMatch = false;
        $current = false;
        foreach ($known as $current) {
            if (($current->Month == $month || null === $month)
                && $current->Year == $year
                && ($current->Day == $day || null === $day)
            ) {
                $foundMatch = true;
                break;
            }
        }
        if (!$foundMatch && count($known) > 0) {
            Console::writeLine("FATAL: Unexpected date value in database; expected $date.");
            return false;
        }
        if (($current && $current->Month > 0 && null === $month)
            || ($current && $current->Day > 0 && null === $day)
        ) {
            Console::writeLine("WARNING: More specific date in database than in incoming data.");
        }
        if (count($known) == 0) {
            Console::writeLine("Adding date: {$date}");
            $fields = [
                'Edition_ID' => $editionObj->Edition_ID,
                'Year' => $year,
            ];
            if (!empty($month)) {
                $fields['Month'] = $month;
            }
            if (!empty($day)) {
                $fields['Day'] = $day;
            }
            $table->insert($fields);
        }
        return true;
    }

    /**
     * Given a publisher string, split apart the publisher name from the street
     * and return a two-part array with the components.
     *
     * @param string $publisher Publisher + street string.
     *
     * @return array
     */
    protected function separateNameAndStreet($publisher)
    {
        $parts = array_map('trim', explode(',', $publisher));
        $name = array_shift($parts);
        $skipParts = ['Publisher', 'Publishers', 'Inc.'];
        while (isset($parts[0]) && in_array($parts[0], $skipParts)) {
            array_shift($parts);
        }
        $street = implode(', ', $parts);
        return [$name, $street];
    }

    /**
     * Normalize a street string for comparison purposes.
     *
     * @param string $street String to normalize.
     *
     * @return string
     */
    protected function normalizeStreet($street)
    {
        return rtrim(
            str_replace(
                [' st.', ' w.', '23rd', 'no. ', '&', 'ave.'],
                [' street', ' west', '23d', '', 'and', 'avenue'],
                strtolower($street)
            ), '.'
        );
    }

    /**
     * Normalize a street string for comparison purposes.
     *
     * @param string $pub String to normalize.
     *
     * @return string
     */
    protected function streetsMatch($street1, $street2)
    {
        return $this->normalizeStreet($street1) == $this->normalizeStreet($street2);
    }

    /**
     * Normalize a publisher string for comparison purposes.
     *
     * @param string $pub String to normalize.
     *
     * @return string
     */
    protected function normalizePublisher($pub)
    {
        return str_replace(
            [', inc.', '. ', 'co.', '&'],
            ['', '.', 'company', 'and'],
            strtolower($pub)
        );
    }

    /**
     * Check if two publisher names are the same after normalization.
     *
     * @param string $pub1 First value to compare
     * @param string $pub2 Second value to compare
     *
     * @return bool
     */
    protected function publishersMatch($pub1, $pub2)
    {
        return $this->normalizePublisher($pub1) == $this->normalizePublisher($pub2);
    }

    /**
     * Normalize a city string for comparison purposes.
     *
     * @param string $city String to normalize.
     *
     * @return string
     */
    protected function normalizeCity($city)
    {
        return str_replace(', n.y', '', strtolower($city));
    }

    /**
     * Check if two city names are the same after normalization.
     *
     * @param string $city1 First value to compare
     * @param string $city2 Second value to compare
     *
     * @return bool
     */
    protected function citiesMatch($city1, $city2)
    {
        return $this->normalizeCity($city1) == $this->normalizeCity($city2);
    }

    /**
     * Validate and store publisher information.
     *
     * @param string $publisher  Publisher string from metadata.
     * @param object $editionObj Edition row
     * @param int    $seriesId   ID of series being worked on.
     *
     * @return bool True on success.
     */
    protected function processPublisher($publisher, $editionObj, $seriesId)
    {
        list ($name, $street) = $this->separateNameAndStreet($publisher['name']);
        if (empty($street)) {
            Console::writeLine("WARNING: No street address; skipping publisher.");
            return true;
        }
        $place = $publisher['place'];
        $spTable = $this->getDbTable('seriespublishers');
        $cityTable = $this->getDbTable('city');
        $pubTable = $this->getDbTable('publisher');
        $result = $spTable->getPublishers($seriesId);
        $match = false;
        foreach ($result as $current) {
            $city = $current['City_ID'] ? $cityTable->getByPrimaryKey($current['City_ID']) : false;
            $pub = $current['Publisher_ID'] ? $pubTable->getByPrimaryKey($current['Publisher_ID']) : false;
            if ($city && $this->citiesMatch($place, $city->City_Name)
                && $pub && $this->publishersMatch($name, $pub->Publisher_Name)
                && $this->streetsMatch($street, $current->Street)
            ) {
                $match = $current->Series_Publisher_ID;
                break;
            }
        }
        if (!$match) {
            Console::writeLine("WARNING: No series/publisher match for $name, $street, $place; skipping publisher.");
            return true;
        }
        if ($editionObj->Preferred_Series_Publisher_ID && $editionObj->Preferred_Series_Publisher_ID != $match) {
            foreach ($this->getDbTable('edition')->getPublishersForEdition($editionObj->Edition_ID) as $ed);
            Console::writeLine("Publisher mismatch in edition.");
            Console::writeLine("Old: {$ed['Publisher_Name']}, {$ed['Street']}, {$ed['City_Name']}");
            Console::writeLine("New: $name, $street, $place");
            if (!Prompt\Confirm::prompt('Change? (y/n) ')) {
                Console::writeLine("FATAL: Aborting ingest due to publisher mismatch.");
                return false;
            }
        }
        if ($editionObj->Preferred_Series_Publisher_ID && $editionObj->Preferred_Series_Publisher_ID == $match) {
            return true;
        }
        Console::writeLine("Updating address to $name, $street, $place");
        $editionObj->Preferred_Series_Publisher_ID = $match;
        $editionObj->save();
        return true;
    }

    /**
     * Normalize and store OCLC number.
     *
     * @param string $oclc       Incoming OCLC number
     * @param object $editionObj Edition row
     *
     * @return bool True on success
     */
    protected function processOclcNum($oclc, $editionObj)
    {
        // strip off non-digits (useless OCLC prefixes):
        foreach ($oclc as $i => $current) {
            $oclc[$i] = preg_replace('/[^0-9]/', '', $current);
        }
        $table = $this->getDbTable('editionsoclcnumbers');
        $known = $table->getOCLCNumbersForEdition($editionObj->Edition_ID);
        $knownArr = [];
        foreach ($known as $current) {
            $knownArr[] = $current->OCLC_Number;
        }
        foreach (array_diff($oclc, $knownArr) as $current) {
            Console::writeLine("Adding OCLC number: {$current}");
            $table->insert(
                [
                    'Edition_ID' => $editionObj->Edition_ID,
                    'OCLC_Number' => $current
                ]
            );
        }
        return true;
    }

    /**
     * Get the source ID for a URL.
     *
     * @param string $url URL to check
     *
     * @return int
     */
    protected function getSourceForUrl($url)
    {
        if (strstr($url, 'lib.niu.edu')) {
            return self::FULLTEXT_SOURCE_NIU;
        } elseif (strstr($url, 'digital.library.villanova.edu')) {
            return self::FULLTEXT_SOURCE_VU;
        } elseif (strstr($url, 'digitalgallery.bgsu.edu')) {
            return self::FULLTEXT_SOURCE_BGSU;
        } elseif (strstr($url, 'archive.org')) {
            return self::FULLTEXT_SOURCE_IA;
        }
        return null;
    }

    /**
     * Validate and store URLs for edition.
     *
     * @param array  $urls       Incoming URLs.
     * @param object $editionObj Edition row
     *
     * @return bool True on success
     */
    protected function processUrl($urls, $editionObj)
    {
        $table = $this->getDbTable('editionsfulltext');
        $known = $table->getFullTextForEdition($editionObj->Edition_ID);
        $knownArr = [];
        foreach ($known as $current) {
            $knownArr[] = $current->Full_Text_URL;
        }
        foreach (array_diff($urls, $knownArr) as $current) {
            $source = $this->getSourceForUrl($current);
            if (null === $source) {
                Console::writeLine('FATAL: Unexpected URL: ' . $current);
                return false;
            }
            Console::writeLine("Adding URL: {$current}");
            $table->insert(
                [
                    'Edition_ID' => $editionObj->Edition_ID,
                    'Full_Text_URL' => $current,
                    'Full_Text_Source_ID' => $source,
                ]
            );
        }
        return true;
    }

    protected function processAuthors($ids, $db)
    {
        if ($this->hasAuthorProblem($ids, $db['authorIds'])) {
            return false;
        }
        $table = $this->getDbTable('editionscredits');
        foreach (array_diff($ids, $db['authorIds']) as $current) {
            Console::writeLine("Attaching author ID $current");
            $table->insert(
                [
                    'Edition_ID' => $db['edition']['Edition_ID'],
                    'Person_ID' => $current,
                    'Role_ID' => self::ROLE_AUTHOR,
                ]
            );
        }
        return true;
    }

    /**
     * Given a URI and type, pull out the numeric ID.
     *
     * @param string $uri  URI of entity
     * @param string $type Type of entity being identified
     *
     * @return int|bool
     */
    protected function extractIdFromDimeNovelsUri($uri, $type)
    {
        $parts = explode('://dimenovels.org/' . $type . '/', $uri);
        return isset($parts[1]) ? $parts[1] : false;
    }

    /**
     * Given an array of uri => text, produce an array of database tag IDs.
     *
     * @param $subjects Associative array of subject data.
     *
     * @return array
     */
    protected function subjectUrisToIds($subjects)
    {
        $tagsUris = $this->getDbTable('tagsuris');
        $tags = $this->getDbTable('tag');

        $ids = [];
        foreach ($subjects as $uri => $text) {
            if ($tagId = $this->extractIdFromDimeNovelsUri($uri, 'Tag')) {
                $id = $tags->getByPrimaryKey($tagId);
                if (!$this->fuzzyCompare($text, $id->Tag)) {
                    Console::writeLine("FATAL: Tag mismatch: $uri, '$text'");
                    return false;
                }
            } else {
                $uriLookup = $tagsUris->getTagsForURI($uri);
                $id = false;
                foreach ($uriLookup as $id) {
                    break;
                }
            }
            if ($id) {
                $ids[$uri] = $id->Tag_ID;
            } else {
                if (!stristr($uri, 'loc.gov')) {
                    Console::writeLine('FATAL: Unexpected subject URI: ' . $uri);
                    return false;
                }
                $tagObj = false;
                $result = $tags->select(['Tag' => $text]);
                foreach ($result as $tagObj) {
                    break;
                }
                if ($tagObj) {
                    Console::writeLine("Upgrading subject: $text");
                    $tagObj->Tag_Type_ID = self::TAGTYPE_LC;
                    $tagObj->save();
                    $ids[$uri] = $tagObj->Tag_ID;
                } else {
                    Console::writeLine("Adding subject: $text");
                    $tags->insert(
                        [
                            'Tag' => $text,
                            'Tag_Type_ID' => self::TAGTYPE_LC,
                        ]
                    );
                    $ids[$uri] = $tags->getLastInsertValue();
                }
                $tagsUris->insert(
                    [
                        'Tag_ID' => $ids[$uri],
                        'URI' => $uri,
                        'Predicate_ID' => self::PREDICATE_OWL_SAMEAS
                    ]
                );
            }
        }
        return $ids;
    }

    protected function processAltTitles($title, $altTitles, $db)
    {
        $articleHelper = $this->articles;
        $allTitles = array_unique(array_merge($altTitles, [$title]));
        $filteredTitles = [];
        foreach (array_diff($altTitles, [$title]) as $currentNeedle) {
            $matched = false;
            if (stristr($currentNeedle, 'and other stories')) {
                // we don't want any "and other stories" titles in this context...
                continue;
            }
            foreach ($allTitles as $currentHaystack) {
                if ($currentNeedle == $currentHaystack) {
                    continue;
                }
                if ($this->fuzzyContains($currentHaystack, $currentNeedle)
                    || $this->fuzzyContains($currentHaystack, $articleHelper->formatTrailingArticles($currentNeedle))
                ) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $filteredTitles[] = $currentNeedle;
            }
        }
        if (!empty($filteredTitles)) {
            $item = $db['item']['Item_ID'];
            $table = $this->getDbTable('itemsalttitles');
            $result = $table->getAltTitles($item);
            $existing = [$db['item']['Item_Name']];
            foreach ($result as $current) {
                $existing[] = $current->Item_AltName;
            }
            foreach ($filteredTitles as $newTitle) {
                $skip = false;
                foreach ($existing as $current) {
                    if ($this->fuzzyCompare($newTitle, $current)
                        || $this->fuzzyContains($current, $newTitle)
                    ) {
                        $skip = true;
                        break;
                    }
                }
                if (!$skip) {
                    $table->insert(['Item_ID' => $item, 'Item_AltName' => $newTitle]);
                    Console::writeLine('Added alternate title: ' . $newTitle);
                }
            }
        }
        return true;
    }

    protected function processSubjects($subjects, $db)
    {
        $item = $db['item']['Item_ID'];
        $subjectIds = $this->subjectUrisToIds($subjects);
        if (false === $subjectIds) {
            return false;
        }
        $itemsTags = $this->getDbTable('itemstags');
        $existingTags = $itemsTags->getTags($item);
        $existingIds = [];
        foreach ($existingTags as $current) {
            $existingIds[] = $current->Tag_ID;
        }
        $missing = array_unique(array_diff($subjectIds, $existingIds));
        if (count($missing) > 0) {
            Console::writeLine("Adding subject IDs: " . implode(', ', $missing));
            foreach ($missing as $id) {
                $itemsTags->insert(['Item_ID' => $item, 'Tag_ID' => $id]);
            }
        }
        return true;
    }

    protected function updateChildWorks($editionObj, $details)
    {
        foreach ($details as $i => $current) {
            list($data, $db) = $current;
            if (!$db) {
                if (!$this->addChildWorkToEdition($editionObj, $data, $i)) {
                    return false;
                }
            } else {
                Console::writeLine("Processing edition ID {$db['edition']['Edition_ID']}");
                if (!$this->updateWorkInDatabase($data, $db)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Given an item ID, return an associative array of id => display string.
     *
     * @param string $item Item ID
     *
     * @return array
     */
    protected function getPeopleForItem($item)
    {
        $table = $this->getDbTable('editionscredits');
        $ids = [];
        foreach ($table->getCreditsForItem($item) as $credit) {
            if ($credit->Role_ID == self::ROLE_AUTHOR) {
                $ids[$credit->Person_ID] = trim($credit->First_Name . ' ' . $credit->Last_Name);
            }
        }
        return $ids;
    }

    /**
     * Support method for getItemMatchCandidatesUsingTitle() -- given raw data and query results,
     * score the results.
     *
     * @param array     $data    Raw data
     * @param \Iterable $options Query results
     *
     * @return array
     */
    protected function evaluateItemMatchTitleCandidates($data, $options, $titleField = 'Item_Name')
    {
        $candidates = [];
        foreach ($options as $current) {
            $confidence = 100;
            $currentCredits = $this->getPeopleForItem($current->Item_ID);
            if (!isset($data['authorIds'])) {
                $data['authorIds'] = [];
            }
            if (count(array_diff($data['authorIds'], array_keys($currentCredits))) != 0) {
                $confidence -= 20;
            }
            if (count(array_intersect($data['authorIds'], array_keys($currentCredits))) != count($data['authorIds'])) {
                $confidence -= 20;
            }
            if (!$this->fuzzyCompare($data['title'], $current->$titleField)) {
                $confidence -= $this->hasMatchingAltTitle($data['title'], $current->Item_ID, isset($current->Item_Name) ? $current->Item_Name : '') ? 10 : 25;
            }
            $candidates[] = [
                'id' => $current->Item_ID,
                'title' => $current->$titleField,
                'authors' => implode(', ', $currentCredits),
                'confidence' => $confidence,
            ];
        }
        return $candidates;
    }

    /**
     * Find item match candidates in the database using title.
     *
     * @param array $data Raw data
     *
     * @return array
     */
    protected function getItemMatchCandidatesUsingTitle($data)
    {
        $table = $this->getDbTable('item');
        // Start by searching for full title; we'll break it down into chunks as we go.
        $pos = strlen($data['title']);
        do {
            $strippedTitle = substr($data['title'], 0, $pos);
            // check to see if we have a title match
            $callback = function ($select) use ($strippedTitle) {
                $select->where->like('Item_Name', $strippedTitle . '%');
            };
            $options = $table->select($callback);
            $candidates = $this->evaluateItemMatchTitleCandidates($data, $options);
            $commaPos = strrpos($strippedTitle, ',');
            $semiPos = strrpos($strippedTitle, ';');
            $pos = $commaPos > $semiPos ? $commaPos : $semiPos;
        } while (count($candidates) == 0 && $pos > 0);
        return $candidates;
    }

    /**
     * Find item match candidates in the database using alternate title.
     *
     * @param array $data Raw data
     *
     * @return array
     */
    protected function getItemMatchCandidatesUsingAltTitle($data)
    {
        $table = $this->getDbTable('itemsalttitles');
        // Start by searching for full title; we'll break it down into chunks as we go.
        $pos = strlen($data['title']);
        do {
            $strippedTitle = substr($data['title'], 0, $pos);
            // check to see if we have a title match
            $callback = function ($select) use ($strippedTitle) {
                $select->where->like('Item_AltName', $strippedTitle . '%');
            };
            $options = $table->select($callback);
            $candidates = $this->evaluateItemMatchTitleCandidates($data, $options, 'Item_AltName');
            $commaPos = strrpos($strippedTitle, ',');
            $semiPos = strrpos($strippedTitle, ';');
            $pos = $commaPos > $semiPos ? $commaPos : $semiPos;
        } while (count($candidates) == 0 && $pos > 0);
        return $candidates;
    }

    /**
     * Reduce a string to alphanumeric chunks
     *
     * @return array
     */
    protected function chunkAndNormalizeString($str)
    {
        static $stopwords = ['a', 'an', 'the', 'of', 'for', 'with', 'or', 'and'];
        $parts = preg_split('/\s+/', $str);
        $callback = function ($s) {
            return preg_replace('/[^a-z0-9]/', '', strtolower($s));
        };
        return array_diff(array_map($callback, $parts), $stopwords);
    }

    /**
     * Given two strings, provide a measure (0-100) of their similarity.
     *
     * @param string $str1 First string
     * @param string $str2 Second string
     *
     * @return int
     */
    protected function measureStringSimilarity($str1, $str2)
    {
        $parts1 = $this->chunkAndNormalizeString($str1);
        $parts2 = $this->chunkAndNormalizeString($str2);
        $smaller = count($parts1) > count($parts2) ? count($parts2) : count($parts1);
        $intersection = array_intersect($parts1, $parts2);
        // Our confidence is never greater than 90%, but the more words that match,
        // the better we feel:
        return ceil(90 * count($intersection) / $smaller);
    }

    /**
     * Find item match candidates in the database using a single author.
     *
     * @param int   $author Author ID
     * @param array $data   Raw data
     *
     * @return array
     */
    protected function getItemMatchCandidatesUsingAuthor($author, $data)
    {
        $ec = $this->getDbTable('editionscredits');
        $candidates = [];
        foreach ($ec->getItemCreditsForPerson($author) as $current) {
            $score = $this->measureStringSimilarity($current['Item_Name'], $data['title']);
            if ($score > 0) {
                $currentCredits = $this->getPeopleForItem($current['Item_ID']);
                $candidates[] = [
                    'id' => $current['Item_ID'],
                    'title' => $current['Item_Name'],
                    'authors' => implode(', ', $currentCredits),
                    'confidence' => $score,
                ];
            } else {
                $table = $this->getDbTable('itemsalttitles');
                foreach ($table->getAltTitles($current['Item_ID']) as $currentAlt) {
                    $score = $this->measureStringSimilarity($currentAlt['Item_AltName'], $data['title']);
                    if ($score > 0) {
                        $currentCredits = $this->getPeopleForItem($current['Item_ID']);
                        $candidates[] = [
                            'id' => $current['Item_ID'],
                            'title' => $currentAlt['Item_AltName'] . ' (alt. title for ' . $current['Item_Name']. ')',
                            'authors' => implode(', ', $currentCredits),
                            'confidence' => $score,
                        ];
                        break;
                    }
                }
            }
        }
        return $candidates;
    }

    /**
     * Find item match candidates in the database using authors.
     *
     * @param array $data Raw data
     *
     * @return array
     */
    protected function getItemMatchCandidatesUsingAuthors($data)
    {
        if (!isset($data['authorIds'])) {
            return [];
        }
        $candidates = [];
        foreach ($data['authorIds'] as $author) {
            $candidates = array_merge($candidates, $this->getItemMatchCandidatesUsingAuthor($author, $data));
        }
        return $candidates;
    }

    /**
     * Deduplicate match candidates array.
     *
     * @param array $candidates Array to deduplicate
     *
     * @return array
     */
    public function deduplicateAndFilterItemMatchCandidates($candidates)
    {
        $new = [];
        foreach ($candidates as $current) {
            // require > 20% confidence for display:
            if ($current['confidence'] <= 20) {
                continue;
            }
            if (!isset($new[$current['id']]) || $new[$current['id']]['confidence'] < $current['confidence']) {
                $new[$current['id']] = $current;
            }
        }
        return $new;
    }

    /**
     * Sort function for match candidates.
     *
     * @param array $left  Left side of comparison
     * @param array $right Right side of comparison
     *
     * @return int
     */
    public function sortCandidates($left, $right)
    {
        return $right['confidence'] - $left['confidence'];
    }

    /**
     * Find item match candidates in the database.
     *
     * @param array $data Raw data
     *
     * @return array
     */
    protected function getItemMatchCandidates($data)
    {
        $candidates = $this->deduplicateAndFilterItemMatchCandidates(
            array_merge(
                $this->getItemMatchCandidatesUsingTitle($data),
                $this->getItemMatchCandidatesUsingAltTitle($data),
                $this->getItemMatchCandidatesUsingAuthors($data)
            )
        );
        usort($candidates, [$this, 'sortCandidates']);
        return $candidates;
    }

    /**
     * Given an array of candidates, return the ID of the perfect match if there is
     * exactly one. Otherwise, return false.
     *
     * @param array $candidates Candidates to examine
     *
     * @return int|bool
     */
    protected function findPerfectItemMatchCandidate($candidates)
    {
        $count = 0;
        $perfect = null;
        foreach ($candidates as $current) {
            if ($current['confidence'] == 100) {
                $perfect = $current['id'];
                $count++;
            }
        }
        return $count == 1 ? $perfect : null;
    }

    protected function addAltTitle($title, $item)
    {
        $table = $this->getDbTable('itemsalttitles');
        $table->insert(['Item_ID' => $item, 'Item_AltName' => $title]);
    }

    /**
     * Create an Item record for the provided data array.
     *
     * @param array $data Incoming data, with 'title' and (optional) 'authorIds' keys.
     * @param int   $type Material type ID to use for item.
     *
     * @return int ID of newly-created Item.
     */
    protected function getItemForNewEdition($data, $type = self::MATERIALTYPE_WORK)
    {
        $candidates = $this->getItemMatchCandidates($data);
        if (count($candidates) > 0) {
            if ($perfect = $this->findPerfectItemMatchCandidate($candidates)) {
                return $perfect;
            }
            $authors = [];
            if (isset($data['authors'])) {
                foreach ($data['authors'] as $current) {
                    $authors[] = $current['name'];
                }
            }
            $authors = count($authors) > 0 ? 'by ' . implode(', ', $authors) : ' - no credits';
            Console::writeLine("Found candidate(s) for match with {$data['title']} $authors\n");
            $options = '0';
            foreach ($candidates as $i => $current) {
                if ($i > 25) {
                    Console::writeLine('...and more options than can be shown!');
                    break;
                }
                $options .= chr(65 + $i);
                Console::writeLine(
                    chr(65 + $i) . '. ' . $current['title']
                    . (!empty($current['authors']) ? ' by ' . $current['authors'] : ' - no credits')
                    . ' (confidence: ' . $current['confidence'] . '%)'
                );
            }
            Console::writeLine("\n0. NONE OF THE ABOVE -- CREATE NEW ITEM.");
            $prompt = new \Zend\Console\Prompt\Char("\nPlease select one: ", $options);
            $char = $prompt->show();
            if ($char !== '0') {
                $response = ord(strtoupper($char)) - 65;
                if (!$this->fuzzyCompare($data['title'], $candidates[$response]['title'])
                    && !$this->hasMatchingAltTitle($data['title'], $candidates[$response]['id'], $candidates[$response]['title'])
                ) {
                    $this->addAltTitle($data['title'], $candidates[$response]['id']);
                }
                return $candidates[$response]['id'];
            }
        }

        // If we made it this far, we need to create a new item.
        $table = $this->getDbTable('item');
        $table->insert(['Item_Name' => $data['title'], 'Material_Type_ID' => $type]);
        $id = $table->getLastInsertValue();
        Console::writeLine("Added item ID {$id} ({$data['title']})");
        return $id;
    }

    protected function addChildWorkToEdition($parentEdition, $data, $pos = 0)
    {
        $item = $this->getItemForNewEdition($data);
        $edName = $parentEdition->Edition_Name;
        $seriesID = $parentEdition->Series_ID;
        $edsTable = $this->getDbTable('edition');
        $edsTable->insert(
            [
                'Edition_Name' => $edName,
                'Series_ID' => $seriesID,
                'Item_ID' => $item,
                'Parent_Edition_ID' => $parentEdition->Edition_ID,
                'Position_In_Parent' => $pos,
            ]
        );
        $newObj = $edsTable->getByPrimaryKey($edsTable->getLastInsertValue());
        Console::writeLine("Added edition ID " . $newObj->Edition_ID);
        return $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $newObj,
                'authorIds' => [],
                'item' => $this->getItemForEdition($newObj)
            ]
        );
    }

    /**
     * Create an edition object in a series.
     *
     * @param object $series Series row object
     * @param int    $item   Item ID number
     * @param int    $pos    Position of edition in series
     *
     * @return object Edition row object
     */
    protected function createEditionInSeries($series, $item, $pos, $data)
    {
        $edName = $this->articles->articleAwareAppend($series->Series_Name, ' edition');
        $seriesID = $series->Series_ID;
        $edsTable = $this->getDbTable('edition');
        $altName = $this->hasMatchingAltTitle($data['title'], $item, '', false, true);
        $edsTable->insert(
            [
                'Edition_Name' => $edName,
                'Series_ID' => $seriesID,
                'Item_ID' => $item,
                'Position' => $pos,
                'Preferred_Item_AltName_ID' => $altName ? $altName : null,
            ]
        );
        return $edsTable->getByPrimaryKey($edsTable->getLastInsertValue());
    }

    /**
     * Add a work to the specified series.
     *
     * @param object $series Series row object.
     * @param array  $data   Incoming data about a single work.
     * @param int    $pos    Position of issue in series.
     *
     * @return object New edition object
     */
    protected function addChildWorkToSeries($series, $data, $pos = 0)
    {
        $item = $this->getItemForNewEdition($data);
        $newObj = $this->createEditionInSeries($series, $item, $pos, $data);
        Console::writeLine("Added edition ID " . $newObj->Edition_ID);
        return $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $newObj,
                'authorIds' => [],
                'item' => $this->getItemForEdition($newObj)
            ]
        );
    }

    /**
     * Create or load an issue in the specified series position.
     *
     * @param object $series Series row object.
     * @param array  $data   Incoming data, with 'title' and (optional) 'authorIds' keys.
     * @param int    $pos    Position of issue in series.
     *
     * @return object|bool New edition object (false on error)
     */
    protected function getChildIssueForSeries($series, $data, $pos = 0)
    {
        // first make sure we don't already have an issue:
        $lookup = $this->getDbTable('edition')
            ->select(['Series_ID' => $series->Series_ID, 'Position' => $pos]);
        if (count($lookup) == 0) {
            $item = $this->getItemForNewEdition($data, self::MATERIALTYPE_ISSUE);
            $newObj = $this->createEditionInSeries($series, $item, $pos, $data);
            Console::writeLine("Added edition ID " . $newObj->Edition_ID);
            return $newObj;
        } else if (count($lookup) == 1) {
            foreach ($lookup as $current) {
                return $current;
            }
        }
        Console::writeLine(
            'ERROR: Found ' . count($lookup) . ' items at position ' . $pos . ' of '
            . $series->Series_Name
        );
        return false;
    }

    /**
     * Check for a match between an incoming title and an alternate title of an Item.
     *
     * @param string $title     Incoming title to check
     * @param int    $itemID    Item to check against
     * @param string $itemTitle Title of item to check against (for additional fuzzy matching)
     * @param bool   $warn      Should we display a warning if we find a match?
     * @param bool   $returnId  Should we return the alt title sequence ID instead of boolean true?
     *
     * @return bool|int
     */
    protected function hasMatchingAltTitle($title, $itemID, $itemTitle, $warn = false, $returnId = false)
    {
        // Check the alt titles table:
        $table = $this->getDbTable('itemsalttitles');
        foreach ($table->getAltTitles($itemID) as $current) {
            $currentAlt = $current->Item_AltName;
            if ($this->fuzzyCompare($title, $currentAlt)) {
                if ($warn) {
                    Console::writeLine(
                        'WARNING: Found match in alt rather than primary title: '
                        . $currentAlt
                    );
                }
                return $returnId ? $current->Sequence_ID : true;
            }
        }

        // Check for partial matches of an "A; or, B" type of title; note that we
        // can't do this in "return ID" mode because these kinds of alt. title
        // match do not exist in the database table and thus have no sequence ID
        // to return.
        list($itemArticle, $itemMainTitle) = $this->articles->separateArticle($itemTitle);
        $titleParts = preg_split('/[;:, ]\s*or[;:, ]/', $itemMainTitle);
        if (!$returnId && count($titleParts) > 1) {
            if ($itemArticle) {
                $titleParts[0] .= ', ' . trim($itemArticle);
            }
            foreach ($titleParts as $part) {
                if ($this->fuzzyCompare($title, $part)) {
                    if ($warn) {
                        Console::writeLine(
                            'WARNING: Partial title match only: ' . $part
                        );
                    }
                    return true;
                }
            }
        }
        return false;
    }

    protected function processTitle($title, $db)
    {
        if ($this->checkItemTitle($db['item'], $title)) {
            return true;
        }
        if ($this->hasMatchingAltTitle($title, $db['item']['Item_ID'], $db['item']['Item_Name'], true)) {
            return true;
        }
        Console::writeLine('FATAL: Unexpected title mismatch.');
        Console::writeLine('Incoming: ' . $title);
        Console::writeLine('Database: ' . $db['item']['Item_Name']);
        return false;
    }

    protected function processExtent($extent, $db)
    {
        $ed = $db['edition'];
        if (!empty($ed->Extent_In_Parent) && $ed->Extent_In_Parent !== $extent) {
            Console::writeLine("FATAL ERROR: Unexpected extent: " . $extent);
            Console::writeLine("Expected: " . $ed->Extent_In_Parent);
            return false;
        }
        if (empty($ed->Parent_Edition_ID)) {
            Console::writeLine("FATAL ERROR: Missing parent ID.");
            return false;
        }
        if (empty($ed->Extent_In_Parent)) {
            Console::writeLine('Adding extent: ' . $extent);
            $ed->Extent_In_Parent = $extent;
            $ed->save();
        }
        return true;
    }

    /**
     * Update a work in the database.
     *
     * @param array $data Incoming data about a single work.
     * @param array $db   Existing data about the same work.
     *
     * @return bool True on success.
     */
    protected function updateWorkInDatabase($data, $db)
    {
        if (!$this->processTitle($data['title'], $db)) {
            return false;
        }
        if (isset($data['altTitles'])) {
            if (!$this->processAltTitles($data['title'], $data['altTitles'], $db)) {
                return false;
            }
        }
        if (isset($data['extent']) && !empty($data['extent'])) {
            if (!$this->processExtent($data['extent'], $db)) {
                return false;
            }
        }
        if (isset($data['authorIds'])) {
            if (!$this->processAuthors($data['authorIds'], $db)) {
                return false;
            }
        }
        if (isset($data['subjects'])) {
            if (!$this->processSubjects($data['subjects'], $db)) {
                return false;
            }
        }
        return $db['edition'];
    }

    /**
     * Given a name string, look up a matching person ID.
     *
     * @param string $str Name string
     *
     * @return int|bool Person ID, or false for no match.
     */
    protected function getPersonIdForString($str)
    {
        $bad = '(dime novelist)';
        if (substr(strtolower($str), -strlen($bad)) === $bad) {
            $str = trim(substr($str, 0, strlen($str) - strlen($bad)));
        }
        $parts = explode(',', $str, 3);
        $last = $parts[0];
        $first = isset($parts[1]) ? $parts[1] : '';
        // handle special case -- last name with date, no first:
        if (preg_match('/\d{4}-\d{4}/', $first)) {
            $parts[2] = $first;
            $first = null;
        }
        $extra = isset($parts[2]) ? (', ' . trim($parts[2])) : '';
        $people = $this->getDbTable('person');
        $query = [
            'First_Name' => trim($first),
            'Last_Name' => trim($last),
        ];
        if (!empty($extra)) {
            $query['Extra_Details'] = $extra;
        }
        $result = $people->select($query);
        if (count($result) == 1) {
            foreach ($result as $current) {
                if ($current->Extra_Details != $extra) {
                    Console::writeLine('Extra detail mismatch in person.');
                    return false;
                }
                return $current->Person_ID;
            }
        }
        return false;
    }

    protected function addAuthorDetails($details)
    {
        foreach ($details as & $match) {
            $match[0]['authorIds'] = [];
            if (isset($match[0]['authors'])) {
                foreach ($match[0]['authors'] as $current) {
                    if (isset($current['uri'])) {
                        $id = $this->getPersonIdForUri($current['uri']);
                    } else {
                        Console::writeLine("WARNING: Missing URI for {$current['name']}...");
                        $id = $this->getPersonIdForString($current['name']);
                    }
                    if (!$id) {
                        $text = isset($current['uri'])
                            ? $current['uri'] . ' (' . $current['name'] . ')'
                            : $current['name'];
                        Console::writeLine("FATAL: Missing Person ID for $text");
                        return false;
                    }
                    $match[0]['authorIds'][] = $id;
                }
            }
            if ($match[1]) {
                $credits = $this->getDbTable('editionscredits')
                    ->getCreditsForEdition($match[1]['edition']['Edition_ID']);
                $match[1]['authorIds'] = [];
                foreach ($credits as $credit) {
                    if ($credit->Role_ID == self::ROLE_AUTHOR) {
                        $match[1]['authorIds'][] = $credit->Person_ID;
                    }
                }
            }
        }
        return $details;
    }

    protected function hasAuthorProblem($incomingList, $storedList)
    {
        $unexpected = array_diff($storedList, $incomingList);
        if (count($unexpected) > 0) {
            $pseudo = $this->getDbTable('pseudonyms');
            $stillUnexpected = [];
            foreach ($unexpected as $current) {
                $matched = false;
                $pseudonyms = $pseudo->getPseudonyms($current);
                foreach ($pseudonyms as $p) {
                    if (in_array($p['Pseudo_Person_ID'], $incomingList)) {
                        $matched = true;
                        Console::writeLine('WARNING: Database contains person ' . $current . ' but incoming data uses pseudonym ' . $p['Pseudo_Person_ID']);
                        break;
                    }
                }
                $real = $pseudo->getRealNames($current);
                foreach ($real as $r) {
                    if (in_array($r['Real_Person_ID'], $incomingList)) {
                        $matched = true;
                        Console::writeLine('WARNING: Database contains person ' . $current . ' but incoming data uses real name ' . $r['Real_Person_ID']);
                        break;
                    }
                    foreach ($pseudo->getPseudonyms($r['Real_Person_ID']) as $realPseudo) {
                        if (in_array($realPseudo['Pseudo_Person_ID'], $incomingList)) {
                            $matched = true;
                            Console::writeLine('WARNING: Database contains person ' . $current . ' but incoming data uses alternate pseudonym ' . $realPseudo['Pseudo_Person_ID']);
                            break 2;
                        }
                    }
                }
                if (!$matched) {
                    $stillUnexpected[] = $current;
                }
            }
            if (count($stillUnexpected) > 0) {
                if (count($incomingList) == 0) {
                    Console::writeLine("WARNING: no incoming authors, but authors found in database.");
                } else {
                    Console::writeLine("Found unexpected author ID(s) in database: " . implode(', ', $unexpected));
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Given a URI, look up a matching person ID.
     *
     * @param string $uri URI
     *
     * @return int|bool Person ID, or false for no match.
     */
    protected function getPersonIdForUri($uri)
    {
        if (!($id = $this->extractIdFromDimeNovelsUri($uri, 'Person'))) {
            $table = $this->getDbTable('peopleuris');
            $result = $table->select(['URI' => $uri]);
            foreach ($result as $curr) {
                $id = $curr['Person_ID'];
            }
        }
        return $id;
    }

    /**
     * Match up incoming contents against an existing series entry; return an array of
     * arrays, each containing an array of incoming contents data as the first element
     * and a matching array of edition/item data (or false) as the second element.
     *
     * @param object $seriesObj Series row for containing series.
     * @param int    $pos       Position of incoming contents within series.
     * @param array  $contents  The 'contents' section of the details from
     * ModsExtractor or equivalent
     *
     * @return array
     */
    protected function synchronizeSeriesEntries($seriesObj, $pos, $contents)
    {
        $params = ['Series_ID' => $seriesObj->Series_ID, 'Position' => $pos];
        $lookup = $this->getDbTable('edition')->select($params);
        $sorted = [];
        foreach ($lookup as $child) {
            $sorted[$child->Replacement_Number][] = $child;
        }
        // Special case -- no matches found; test with an empty array.
        if (empty($sorted)) {
            return $this->synchronizeSeriesEntriesHelper([], $contents);
        }

        // When replacement numbers are involved, let's try each one separately
        // until we find one that works....
        foreach ($sorted as $replacementNo => $children) {
            if (count($sorted) > 1) {
                Console::writeLine("Trying replacement no. $replacementNo");
            }
            if (count($children) > 1) {
                Console::writeLine("Multiple editions found at same position.");
                Console::writeLine("Please pick one:");
                $options = '';
                foreach ($children as $i => $current) {
                    $letter = chr(65 + $i);
                    $options .= $letter;
                    Console::writeLine($letter . '. ' . $current->Edition_Name);
                }
                $prompt = new \Zend\Console\Prompt\Char("\nPlease select one: ", $options);
                $char = strtoupper($prompt->show());
                $children = [$children[ord($char) - 65]];
            }
            $success = $this->synchronizeSeriesEntriesHelper($children, $contents);
            if ($success) {
                return $success;
            }
        }
        return false;
    }

    /**
     * Helper method for synchronizeSeriesEntries(); allows us to evaluate titles
     * with replacement numbers one group at a time.
     *
     * @param array $lookup   An array of editions to check
     * @param array $contents The 'contents' section of the details from
     * ModsExtractor or equivalent
     *
     * @return array
     */
    protected function synchronizeSeriesEntriesHelper($lookup, $contents)
    {
        $children = [];
        $titlesChecked = [];
        foreach ($lookup as $child) {
            $item = $this->getItemForEdition($child);
            // If it's an issue, we need to load its children; otherwise, we should
            // treat it as flat:
            if ($item['Material_Type_ID'] == self::MATERIALTYPE_ISSUE) {
                return $this->synchronizeChildren($child, $contents);
            } else {
                $children[] = [
                    'edition' => $child,
                    'item' => $item,
                ];
            }
        }
        $result = [];
        foreach ($contents as $currentContent) {
            $match = false;
            foreach ($children as & $currentChild) {
                $titlesChecked[] = $currentChild['item']['Item_Name'] . ' vs. ' . $currentContent['title'];
                if ($this->checkItemTitles($currentChild['item'], $currentContent)) {
                    $match = true;
                    $result[] = [$currentContent, $currentChild];
                    $currentChild['matched'] = true;
                    break;
                }
            }
            if (!$match) {
                $result[] = [$currentContent, false];
            }
        }

        // Fail if we have any existing data not matched up with new data....
        foreach ($children as $child) {
            if (!isset($child['matched'])) {
                foreach ($titlesChecked as $titleChecked) {
                    Console::writeLine("Title checked: " . $titleChecked);
                }
                Console::writeLine("FATAL: No series match found for edition {$child['edition']->Edition_ID}");
                return false;
            }
        }
        return $result;
    }

    /**
     * Match up incoming contents against an existing edition; return an array of
     * arrays, each containing an array of incoming contents data as the first element
     * and a matching array of edition/item data (or false) as the second element.
     *
     * @param object $editionObj Edition row for top-level edition.
     * @param array  $contents   The 'contents' section of the details from
     * ModsExtractor or equivalent
     *
     * @return array
     */
    protected function synchronizeChildren($editionObj, $contents)
    {
        $lookup = $this->getDbTable('edition')
            ->select(['Parent_Edition_ID' => $editionObj->Edition_ID]);
        $children = [];
        foreach ($lookup as $child) {
            $children[] = [
                'edition' => $child,
                'item' => $this->getItemForEdition($child)
            ];
        }

        $result = [];
        foreach ($contents as $currentContent) {
            $match = false;
            foreach ($children as & $currentChild) {
                if ($this->checkItemTitles($currentChild['item'], $currentContent)) {
                    $match = true;
                    $result[] = [$currentContent, $currentChild];
                    $currentChild['matched'] = true;
                    break;
                }
            }
            if (!$match) {
                $result[] = [$currentContent, false];
            }
        }

        // Fail if we have any existing data not matched up with new data....
        foreach ($children as $child) {
            if (!isset($child['matched'])) {
                Console::writeLine("Unmatched item: {$child['item']['Item_Name']}");
                foreach ($contents as $current) {
                    Console::writeLine("Possible match: " . $current['title']);
                }
                Console::writeLine("FATAL: No child match found for edition {$child['edition']->Edition_ID}");
                return false;
            }
        }
        return $result;
    }

    /**
     * Check the consistency of the incoming series data with existing database entries.
     *
     * @param array  $details    Details from ModsExtractor or equivalent
     * @param object $editionObj Edition row
     * @param array  $series     Series summary array (see getSeriesForEdition)
     *
     * @return True if all is well.
     */
    protected function validateSeries($details, $editionObj, $series)
    {
        if (!isset($details['series'])) {
            Console::writeLine('No series found.');
            return false;
        }
        $expectedNumber = intval($editionObj->Position);
        foreach ($details['series'] as $seriesName => $number) {
            $actualNumber = intval(preg_replace('/[^0-9]/', '', $number));
            //Console::writeLine("Comparing {$expectedNumber} to {$actualNumber}...");
            if ($actualNumber == $expectedNumber && $this->checkSeriesTitle($series, $seriesName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize a string for fuzzy comparison.
     *
     * @param string $str String to normalize.
     *
     * @return bool
     */
    protected function fuzz($str)
    {
        $regex = '/[^a-z0-9]/';
        return preg_replace($regex, '', strtolower($str));
    }

    /**
     * Are $str1 and $str2 the same using fuzzy comparison?
     *
     * @param string $str1 First string to compare
     * @param string $str2 Second string to compare
     *
     * @return bool
     */
    protected function fuzzyCompare($str1, $str2)
    {
        //Console::writeLine("Comparing {$str1} to {$str2}...");
        return $this->fuzz($str1) == $this->fuzz($str2);
    }

    /**
     * Does $haystack contain $needle (using fuzzy comparison)?
     *
     * @param string $haystack Haystack
     * @param string $needle   Needle
     *
     * @return bool
     */
    protected function fuzzyContains($haystack, $needle)
    {
        return strstr($this->fuzz($haystack), $this->fuzz($needle));
    }

    /**
     * Do a fuzzy compare to validate an item title.
     *
     * @param array  $item  Item summary array (see getItemForEdition)
     * @param string $title Title from incoming data, to be checked.
     *
     * @return bool
     */
    protected function checkItemTitle($item, $title)
    {
        $itemTitle = (isset($item['Item_AltName']) && !empty($item['Item_AltName']))
            ? $item['Item_AltName'] : $item['Item_Name'];
        $match = $this->fuzzyCompare($title, $itemTitle);
        if (!$match) {
            $stripped1 = $this->articles->separateArticle($title)[1];
            $stripped2 = $this->articles->separateArticle($itemTitle)[1];
            if (strlen($stripped1) > strlen($stripped2)) {
                $longer = $stripped1;
                $shorter = $stripped2;
            } else {
                $shorter = $stripped1;
                $longer = $stripped2;
            }
            if ($this->fuzzyContains($longer, $shorter)) {
                Console::writeLine("WARNING: inexact title match {$title} vs. {$itemTitle}");
                return true;
            }
        }
        return $match;
   }

    /**
     * Do a fuzzy compare to validate if any of the incoming titles match. Checks
     * main title and alt titles.
     *
     * @param array $item           Item summary array (see getItemForEdition)
     * @param array $currentContent Incoming data to be checked (must contain 'title'
     * key; may contain 'altTitles' key)
     *
     * @return bool
     */
    protected function checkItemTitles($item, $currentContent)
    {
        if ($this->checkItemTitle($item, $currentContent['title'])) {
            return true;
        }
        if (isset($currentContent['altTitles'])) {
            foreach ($currentContent['altTitles'] as $title) {
                if ($this->checkItemTitle($item, $title)) {
                    return true;
                }
            }
        }
        if ($this->hasMatchingAltTitle($currentContent['title'], $item['Item_ID'], $item['Item_Name'])) {
            return true;
        }
        return false;
    }

    /**
     * Do a fuzzy compare to validate a series title.
     *
     * @param array  $series Series summary array (see getSeriesForEdition)
     * @param string $title  Title from incoming data, to be checked.
     *
     * @return bool
     */
    protected function checkSeriesTitle($series, $title)
    {
        $seriesTitle = (isset($series['Series_AltName']) && !empty($series['Series_AltName']))
            ? $series['Series_AltName'] : $series['Series_Name'];
        return $this->fuzzyCompare($title, $seriesTitle);
    }

    /**
     * Given an edition row object, return a summary item array.
     *
     * @param object $rowObj Edition row
     *
     * @return array
     */
    protected function getItemForEdition($rowObj)
    {
        $itemTable = $this->getDbTable('item');
        $itemObj = $itemTable->getByPrimaryKey($rowObj->Item_ID);
        $item = $itemObj->toArray();
        if (!empty($rowObj->Preferred_Item_AltName_ID)) {
            $ian = $this->getDbTable('itemsalttitles');
            $tmpRow = $ian->select(
                array('Sequence_ID' => $rowObj->Preferred_Item_AltName_ID)
            )->current();
            $item['Item_AltName'] = $tmpRow['Item_AltName'];
        }
        return $item;
    }

    /**
     * Given an edition row object, return a summary series array.
     *
     * @param object $rowObj Edition row
     *
     * @return array
     */
    protected function getSeriesForEdition($rowObj)
    {
        $seriesTable = $this->getDbTable('series');
        $seriesObj = $seriesTable->getByPrimaryKey($rowObj->Series_ID);
        $series = $seriesObj->toArray();
        if (!empty($rowObj->Preferred_Series_AltName_ID)) {
            $san = $this->getDbTable('seriesalttitles');
            $tmpRow = $san->select(
                array('Sequence_ID' => $rowObj->Preferred_Series_AltName_ID)
            )->current();
            $series['Series_AltName'] = $tmpRow['Series_AltName'];
        }
        return $series;
    }
}
