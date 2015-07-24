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
    const MATERIALTYPE_WORK = 1;
    const ROLE_AUTHOR = 1;

    /**
     * Standard ingest action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        foreach ($this->getEditionsFromSolr() as $edition) {
            if (!$this->loadExistingEdition($edition)) {
                break;
            }
        }
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
        return $this->updateDatabase($editionObj, $series, $details);
    }

    protected function updateDatabase($editionObj, $series, $details)
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
        return $this->updateWorks($editionObj, $details['contents']);
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

    protected function separateNameAndStreet($publisher)
    {
        $parts = array_map('trim', explode(',', $publisher));
        $name = array_shift($parts);
        if ($parts[0] == 'Publisher') {
            array_shift($parts);
        }
        $street = implode(', ', $parts);
        return [$name, $street];
    }

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
            if ($city && $place == $city->City_Name
                && $pub && $name == $pub->Publisher_Name
                && $street == $current->Street
            ) {
                $match = $current->Series_Publisher_ID;
                break;
            }
        }
        if (!$match) {
            Console::writeLine("FATAL: No series/publisher match for $name, $street, $place");
            return false;
        }
        if ($editionObj->Preferred_Series_Publisher_ID && $editionObj->Preferred_Series_Publisher_ID != $match) {
            Console::writeLine("FATAL: Publisher mismatch in edition.");
            return false;
        }
        if ($editionObj->Preferred_Series_Publisher_ID && $editionObj->Preferred_Series_Publisher_ID == $match) {
            return true;
        }
        Console::writeLine("Updating address to $name, $street, $place");
        $editionObj->Preferred_Series_Publisher_ID = $match;
        $editionObj->save();
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

    protected function processSubjects($subjects, $db)
    {
        Console::writeLine("TODO: processSubjects()");
        return true;
    }

    protected function updateWorks($editionObj, $details)
    {
        foreach ($details as $i => $current) {
            list($data, $db) = $current;
            if (!$db) {
                if (!$this->addChildWorkToDatabase($editionObj, $data, $i)) {
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

    protected function getPersonIdsForItem($item)
    {
        $table = $this->getDbTable('editionscredits');
        $ids = [];
        foreach ($table->getCreditsForItem($item) as $credit) {
            if ($credit->Role_ID == self::ROLE_AUTHOR) {
                $ids[] = $credit->Person_ID;
            }
        }
        return $ids;
    }

    protected function getItemForNewEdition($data)
    {
        // trim article for search purposes
        $strippedTitle = ($pos = strrpos($data['title'], ','))
            ? substr($data['title'], 0, $pos) : $data['title'];
        $table = $this->getDbTable('item');

        $callback = function ($select) use ($strippedTitle) {
            $select->where->like('Item_Name', $strippedTitle . '%');
        };
        $options = $table->select($callback);
        foreach ($options as $current) {
            $currentCredits = $this->getPersonIdsForItem($current->Item_ID);
            if (count($data['authorIds']) > 0 && count(array_diff($data['authorIds'], $currentCredits) == 0)) {
                Console::writeLine("Matched existing item ID {$current->Item_ID}");
                return $current->Item_ID;
            }
        }

        // If we made it this far, we need to create a new item.
        $table->insert(['Item_Name' => $data['title'], 'Material_Type_ID' => self::MATERIALTYPE_WORK]);
        $id = $table->getLastInsertValue();
        Console::writeLine("Added item ID {$id} ({$data['title']})");
        return $id;
    }

    protected function addChildWorkToDatabase($parentEdition, $data, $pos = 0)
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
        $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $newObj,
                'authorIds' => [],
                'item' => $this->getItemForEdition($newObj)
            ]
        );
        return true;
    }

    protected function processTitle($title, $db)
    {
        if (!$this->fuzzyCompare($title, $db['item']['Item_Name'])) {
            Console::writeLine("Unexpected title mismatch; {$title} vs. {$db['item']['Item_Name']}");
            return false;
        }
        return true;
    }

    protected function updateWorkInDatabase($data, $db)
    {
        if (!$this->processTitle($data['title'], $db)) {
            return false;
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

    protected function getEditionsFromSolr()
    {
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'));
        $query = $settings->solrQueryField . ':"http://dimenovels.org/*"';
        $field = $settings->solrQueryField;
        $url = (string)$settings->solrUrl . '?q=' . urlencode($query) . '&wt=json'
            . '&rows=10000&fl=' . urlencode($field);
        Console::writeLine("Querying {$settings->solrUrl} for $query...");
        $solr = json_decode(file_get_contents($url));
        $editions = [];
        foreach ($solr->response->docs as $current) {
            $parts = explode('/', $current->{$settings->solrQueryField});
            $currentEd = array_pop($parts);
            $editions[] = $currentEd;
        }
        return $editions;
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
