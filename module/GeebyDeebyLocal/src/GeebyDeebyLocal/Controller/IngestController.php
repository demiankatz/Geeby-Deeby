<?php
/**
 * Ingest controller
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
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;
use GeebyDeebyLocal\Ingest\ModsExtractor, Zend\Console\Console;

/**
 * Ingest controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IngestController extends \GeebyDeeby\Controller\AbstractBase
{
    const FULLTEXT_SOURCE_NIU = 10;
    const ROLE_AUTHOR = 1;

    /**
     * Standard ingest action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $edition = 5865;
        $this->loadExistingEdition($edition);
    }

    protected function loadExistingEdition($edition)
    {
        Console::writeLine("Loading existing edition (id = {$edition})");
        $rawMods = $this->getModsForEdition('http://dimenovels.org/Edition/' . $edition);
        if (!$rawMods) {
            Console::writeLine('Could not retrieve MODS.');
            return false;
        }
        $mods = simplexml_load_string($rawMods);
        $editionObj = $this->getDbTable('edition')->getByPrimaryKey($edition);
        $series = $this->getSeriesForEdition($editionObj);

        $extractor = new ModsExtractor();
        $details = $extractor->getDetails($mods);

        if (!$this->validateSeries($details, $editionObj, $series)) {
            Console::writeLine('Series validation failed.');
            return false;
        }

        $childDetails = $this->synchronizeChildren($editionObj, $details['contents']);
        if (!$childDetails) {
            return false;
        }
        $childDetails = $this->addAuthorDetails($childDetails);
        if (!$childDetails) {
            return false;
        }
        $details['contents'] = $childDetails;
        return $this->updateDatabase($editionObj, $details);
    }

    protected function updateDatabase($editionObj, $details)
    {
        $item = $this->getItemForEdition($editionObj);
        if (isset($details['date'])) {
            if (!$this->processDate($details['date'], $editionObj)) {
                return false;
            }
        }
        if (isset($details['publisher'])) {
            if (!$this->processPublisher($details['publisher'], $editionObj)) {
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
        return $this->updateWorks($details['contents']);
    }

    protected function processDate($date, $editionObj)
    {
        list($year, $month, $day) = explode('-', $date);
        $table = $this->getDbTable('editionsreleasedates');
        $known = $table->getDatesForEdition($editionObj->Edition_ID);
        $foundMatch = false;
        foreach ($known as $current) {
            if ($current->Month == $month && $current->Year == $year && $current->Day == $day) {
                $foundMatch = true;
            }
        }
        if (!$foundMatch && count($known) > 0) {
            Console::writeLine("FATAL: Unexpected date value in database.");
            return false;
        }
        if (count($known) == 0) {
            Console::writeLine("Adding date: {$date}");
            $table->insert(
                [
                    'Edition_ID' => $editionObj->Edition_ID,
                    'Year' => $year,
                    'Month' => $month,
                    'Day' => $day
                ]
            );
        }
        return true;
    }

    protected function processPublisher($publisher, $editionObj)
    {
        Console::writeLine("TODO: processPublisher()");
        return true;
    }

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

    protected function processUrl($urls, $editionObj)
    {
        // check for unexpected URLs (right now we assume everything is from NIU):
        foreach ($urls as $current) {
            if (!strstr($current, 'lib.niu.edu')) {
                Console::writeLine('FATAL: Unexpected URL: ' . $current);
                return false;
            }
        }
        $table = $this->getDbTable('editionsfulltext');
        $known = $table->getFullTextForEdition($editionObj->Edition_ID);
        $knownArr = [];
        foreach ($known as $current) {
            $knownArr[] = $current->Full_Text_URL;
        }
        foreach (array_diff($urls, $knownArr) as $current) {
            Console::writeLine("Adding URL: {$current}");
            $table->insert(
                [
                    'Edition_ID' => $editionObj->Edition_ID,
                    'Full_Text_URL' => $current,
                    'Full_Text_Source_ID' => self::FULLTEXT_SOURCE_NIU,
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
            Console::writeLine("Adding author ID $current");
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

    protected function processSubjects($subjects, $db)
    {
        Console::writeLine("TODO: processSubjects()");
        return true;
    }

    protected function updateWorks($details)
    {
        foreach ($details as $current) {
            list($data, $db) = $current;
            if (!$db) {
                if (!$this->addWorkToDatabase($data)) {
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

    protected function addWorkToDatabase($data)
    {
        Console::writeLine("TODO: addWorkToDatabase()");
        return true;
    }

    protected function updateWorkInDatabase($data, $db)
    {
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
        return true;
    }

    protected function addAuthorDetails($details)
    {
        foreach ($details as & $match) {
            $match[0]['authorIds'] = [];
            foreach ($match[0]['authors'] as $current) {
                if (!isset($current['uri'])) {
                    Console::writeLine("FATAL: Missing URI for {$current['name']}...");
                    return false;
                }
                $id = $this->getPersonIdForUri($current['uri']);
                if (!$id) {
                    Console::writeLine("FATAL: Missing Person ID for {$current['uri']}");
                    return false;
                }
                $match[0]['authorIds'][] = $id;
            }
            if ($match[1]) {
                $credits = $this->getDbTable('editionscredits')
                    ->getCreditsForEdition($match[1]['edition']->Edition_ID);
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
            Console::writeLine("Found unexpected author ID(s) in database: " . implode(', ', $unexpected));
            return true;
        }
        return false;
    }

    protected function getPersonIdForUri($uri)
    {
        $base = 'http://dimenovels.org/Person/';
        if (substr($uri, 0, strlen($base)) == $base) {
            $id = str_replace($base, '', $uri);
        } else {
            $table = $this->getDbTable('peopleuris');
            $result = $table->select(['URI' => $uri]);
            $id = false;
            foreach ($result as $curr) {
                $id = $curr['Person_ID'];
            }
        }
        return $id;
    }

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
                if ($this->checkItemTitle($currentChild['item'], $currentContent['title'])) {
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
                Console::writeLine("FATAL: No match found for edition {$child['edition']->Edition_ID}");
                return false;
            }
        }
        return $result;
    }

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

    protected function fuzzyCompare($str1, $str2)
    {
        //Console::writeLine("Comparing {$str1} to {$str2}...");
        $regex = '/[^a-z0-9]/';
        $str1 = preg_replace($regex, '', strtolower($str1));
        $str2 = preg_replace($regex, '', strtolower($str2));
        return $str1 == $str2;
    }

    protected function checkItemTitle($item, $title)
    {
        $itemTitle = (isset($item['Item_AltName']) && !empty($item['Item_AltName']))
            ? $item['Item_AltName'] : $item['Item_Name'];
        return $this->fuzzyCompare($title, $itemTitle);
    }

    protected function checkSeriesTitle($series, $title)
    {
        $seriesTitle = (isset($series['Series_AltName']) && !empty($series['Series_AltName']))
            ? $series['Series_AltName'] : $series['Series_Name'];
        return $this->fuzzyCompare($title, $seriesTitle);
    }

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

    protected function getModsForEdition($edition)
    {
        $cache = '/tmp/gbdb_' . md5($edition);
        if (file_exists($cache)) {
            return file_get_contents($cache);
        }
    
        // Get MODS identifier from Solr:
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'));
        $query = $settings->solrQueryField . ':"' . $edition . '"';
        $field = $settings->solrIdField;
        $url = (string)$settings->solrUrl . '?q=' . urlencode($query) . '&wt=json'
            . '&fl=' . urlencode($field);
        Console::writeLine("Querying {$settings->solrUrl} for $query...");
        $solr = json_decode(file_get_contents($url));
        $pid = isset($solr->response->docs[0]->PID)
            ? $solr->response->docs[0]->PID : false;
        if (!$pid) {
            return false;
        }

        // Retrieve MODS from repository:
        $modsUrl = sprintf($settings->modsUrl, $pid);
        Console::writeLine("Retrieving $modsUrl...");
        $mods = file_get_contents($modsUrl);
        file_put_contents($cache, $mods);
        return $mods;
    }
}
