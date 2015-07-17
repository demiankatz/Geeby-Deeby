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
    /**
     * Standard ingest action.
     *
     * @return mixed
     */
    public function indexAction()
    {
        $edition = 5865;
        return $this->loadExistingEdition($edition);
    }

    protected function loadExistingEdition($edition)
    {
        $rawMods = $this->getModsForEdition('http://dimenovels.org/Edition/' . $edition);
        if (!$rawMods) {
            Console::writeLine('Could not retrieve MODS.');
            return;
        }
        $mods = simplexml_load_string($rawMods);
        $editionObj = $this->getDbTable('edition')->getByPrimaryKey($edition);
        $item = $this->getItemForEdition($editionObj);
        $series = $this->getSeriesForEdition($editionObj);

        $extractor = new ModsExtractor();
        $details = $extractor->getDetails($mods);
        print_r($details);

        if (!$this->validateSeries($details, $editionObj, $series)) {
            Console::writeLine('Series validation failed.');
            return;
        }

        $childDetails = $this->synchronizeChildren($editionObj, $details['contents']);
        if (!$childDetails) {
            return;
        }
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
            Console::writeLine("Comparing {$expectedNumber} to {$actualNumber}...");
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
