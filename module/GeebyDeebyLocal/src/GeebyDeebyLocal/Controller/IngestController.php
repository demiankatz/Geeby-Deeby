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
use GeebyDeebyLocal\Ingest\ModsExtractor, Zend\Console\Console, Zend\Console\Prompt;

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
    // constant values drawn from dimenovels.org database:
    const FULLTEXT_SOURCE_NIU = 10;
    const MATERIALTYPE_WORK = 1;
    const PREDICATE_OWL_SAMEAS = 2;
    const ROLE_AUTHOR = 1;
    const TAGTYPE_LC = 1;

    protected $fedora;
    protected $solr;

    public function __construct()
    {
        $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'));
        $this->solr = new \GeebyDeebyLocal\Ingest\SolrHarvester($settings);
        $this->fedora = new \GeebyDeebyLocal\Ingest\FedoraHarvester($settings->modsUrl, $this->solr);
    }

    /**
     * Harvest existing editions to a directory for later processing.
     *
     * @return mixed
     */
    public function harvestexistingAction()
    {
        $dir = rtrim($this->params()->fromRoute('dir'), '/');
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                Console::writeLine("Cannot create directory '$dir'");
                return;
            }
        }
        $count = 0;
        foreach ($this->solr->getExistingEditions() as $edition) {
            $rawMods = $this->fedora->getModsForEdition('https://dimenovels.org/Edition/' . $edition);
            if (!$rawMods) {
                Console::writeLine("Could not retrieve MODS for $edition.");
                return;
            }
            file_put_contents($dir . '/' . $count . '.mods', $rawMods);
            $count++;
        }
        file_put_contents($dir . '/job.json', ['type' => 'existing', 'count' => $count]);
        Console::writeLine("Successfully harvested $count records.");
    }

    /**
     * Harvest records from a series to a directory for later processing.
     *
     * @return mixed
     */
    public function harvestseriesAction()
    {
        $dir = rtrim($this->params()->fromRoute('dir'), '/');
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                Console::writeLine("Cannot create directory '$dir'");
                return;
            }
        }
        $series = $this->params()->fromRoute('series');
        $seriesObj = $this->getSeriesByTitle($series);
        if (!$seriesObj) {
            Console::writeLine("Cannot find series match for $series");
            return;
        }
        $entries = $this->solr->getSeriesEntries($series);
        $count = 0;
        foreach ($entries as $pid) {
            $rawMods = $this->fedora->getModsForPid($pid);
            if (!$rawMods) {
                Console::writeLine("Could not retrieve MODS for $pid.");
                return;
            }
            file_put_contents($dir . '/' . $count . '.mods', $rawMods);
            $count++;
        }
        file_put_contents($dir . '/job.json', ['type' => 'series', 'id' => $seriesObj->Series_ID, 'count' => $count]);
        Console::writeLine("Successfully harvested $count records.");
    }

    /**
     * Ingest existing editions (by matching URIs) action.
     *
     * @return mixed
     */
    public function existingAction()
    {
        $editions = $this->solr->getExistingEditions();
        $total = count($editions);
        $success = 0;
        foreach ($editions as $edition) {
            if (!$this->loadExistingEdition($edition)) {
                if (Prompt\Confirm::prompt('Continue with next item anyway? (y/n) ')) {
                    continue;
                }
                break;
            }
            $success++;
        }
        Console::writeLine("Successfully processed $success of $total editions.");
    }

    /**
     * Ingest series entries (by searching series title) action.
     *
     * @return mixed
     */
    public function seriesAction()
    {
        // TODO
        $series = $this->params()->fromRoute('series');
        $seriesObj = $this->getSeriesByTitle($series);
        if (!$seriesObj) {
            Console::writeLine("Cannot find series match for $series");
            return;
        }
        $entries = $this->solr->getSeriesEntries($series);
        $total = count($entries);
        $success = 0;
        foreach ($entries as $pid) {
            if (!$this->loadSeriesEntry($pid, $seriesObj)) {
                if (Prompt\Confirm::prompt('Continue with next item anyway? (y/n) ')) {
                    continue;
                }
                break;
            }
            $success++;
        }
        Console::writeLine("Successfully processed $success of $total editions.");
    }

    protected function loadSeriesEntry($pid, $seriesObj)
    {
        Console::writeLine("Loading series entry (pid = $pid)");
        $rawMods = $this->fedora->getModsForPid($pid);
        if (!$rawMods) {
            Console::writeLine('Could not retrieve MODS.');
            return false;
        }
        $mods = simplexml_load_string($rawMods);
        $extractor = new ModsExtractor();
        $details = $extractor->getDetails($mods);
        if (count($details['contents']) > 1) {
            // For now, we expect just one work per item; TODO: multi-part issues
            Console::writeLine('FATAL: More contents than expected....');
            return false;
        }
        $pos = preg_replace('/[^0-9]/', '', current($details['series']));
        $childDetails = $this->synchronizeSeriesEntries($seriesObj, $pos, $details['contents']);
        if (!$childDetails) {
            return false;
        }
        $childDetails = $this->addAuthorDetails($childDetails);
        if (!$childDetails) {
            return false;
        }
        $details['contents'] = $childDetails;
        return $this->updateDatabaseForFlatEdition($seriesObj, $pos, $details);
    }

    protected function getSeriesByTitle($title)
    {
        $table = $this->getDbTable('series');
        $result = $table->select(['Series_Name' => $title]);
        if (count($result) != 1) {
            Console::writeLine('Unexpected result count: ' . count($result));
            return false;
        }
        foreach ($result as $current) {
            return $current;
        }
    }

    protected function loadExistingEdition($edition)
    {
        Console::writeLine("Loading existing edition (id = {$edition})");
        $rawMods = $this->fedora->getModsForEdition('https://dimenovels.org/Edition/' . $edition);
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
        return $this->updateDatabaseForHierarchicalEdition($editionObj, $series, $details);
    }

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

    protected function updateDatabaseForHierarchicalEdition($editionObj, $series, $details)
    {
        if (!$this->setTopLevelDetails($editionObj, $series, $details)) {
            return false;
        }
        return $this->updateChildWorks($editionObj, $details['contents']);
    }

    protected function processDate($date, $editionObj)
    {
        $parts = explode('-', $date);
        $year = isset($parts[0]) ? $parts[0] : null;
        $month = isset($parts[1]) ? $parts[1] : null;
        $day = isset($parts[2]) ? $parts[2] : null;
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

    protected function normalizeStreet($street)
    {
        return str_replace(
            [' st.', ' w.', '23rd', 'no. '],
            [' street', ' west', '23d', ''],
            strtolower($street)
        );
    }
    protected function streetsMatch($street1, $street2)
    {
        return $this->normalizeStreet($street1) == $this->normalizeStreet($street2);
    }

    protected function normalizePublisher($pub)
    {
        return str_replace(', inc.', '', strtolower($pub));
    }

    protected function publishersMatch($pub1, $pub2)
    {
        return $this->normalizePublisher($pub1) == $this->normalizePublisher($pub2);
    }

    protected function normalizeCity($city)
    {
        return str_replace(', n.y', '', strtolower($city));
    }

    protected function citiesMatch($city1, $city2)
    {
        return $this->normalizeCity($city1) == $this->normalizeCity($city2);
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
            if ($city && $this->citiesMatch($place, $city->City_Name)
                && $pub && $this->publishersMatch($name, $pub->Publisher_Name)
                && $this->streetsMatch($street, $current->Street)
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

    protected function subjectUrisToIds($subjects)
    {
        $tagsUris = $this->getDbTable('tagsuris');
        $tags = $this->getDbTable('tag');

        $ids = [];
        foreach ($subjects as $uri => $text) {
            $uriLookup = $tagsUris->getTagsForURI($uri);
            $id = false;
            foreach ($uriLookup as $id) {
                break;
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
        $articleHelper = $this->getServiceLocator()->get('GeebyDeeby\Articles');
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
        $missing = array_diff($subjectIds, $existingIds);
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
            if (count($data['authorIds']) > 0
                && count(array_diff($data['authorIds'], $currentCredits) == 0)
                && count(array_intersect($data['authorIds'], $currentCredits)) == count($data['authorIds'])
            ) {
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

    protected function addChildWorkToSeries($series, $data, $pos = 0)
    {
        $item = $this->getItemForNewEdition($data);
        $edName = $this->getServiceLocator()->get('GeebyDeeby\Articles')
            ->articleAwareAppend($series->Series_Name, ' edition');
        $seriesID = $series->Series_ID;
        $edsTable = $this->getDbTable('edition');
        $edsTable->insert(
            [
                'Edition_Name' => $edName,
                'Series_ID' => $seriesID,
                'Item_ID' => $item,
                'Position' => $pos,
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

    protected function processTitle($title, $db)
    {
        if ($this->fuzzyCompare($title, $db['item']['Item_Name'])) {
            return true;
        }
        $table = $this->getDbTable('itemsalttitles');
        foreach ($table->getAltTitles($db['item']['Item_ID']) as $current) {
            $currentAlt = $current->Item_AltName;
            if ($this->fuzzyCompare($title, $currentAlt)) {
                Console::writeLine(
                    'WARNING: Found match in alt rather than primary title: '
                    . $currentAlt
                );
                return true;
            }
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

    protected function getPersonIdForString($str)
    {
        $bad = '(dime novelist)';
        if (substr(strtolower($str), -strlen($bad)) === $bad) {
            $str = trim(substr($str, 0, strlen($str) - strlen($bad)));
        }
        $parts = explode(',', $str, 3);
        $last = $parts[0];
        $first = $parts[1];
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
                        $text = isset($current['uri']) ? $current['uri'] : $current['name'];
                        Console::writeLine("FATAL: Missing Person ID for $text");
                        return false;
                    }
                    $match[0]['authorIds'][] = $id;
                }
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
                }
                if (!$matched) {
                    $stillUnexpected[] = $current;
                }
            }
            if (count($stillUnexpected) > 0) {
                Console::writeLine("Found unexpected author ID(s) in database: " . implode(', ', $unexpected));
                return true;
            }
        }
        return false;
    }

    protected function getPersonIdForUri($uri)
    {
        $base = 'https://dimenovels.org/Person/';
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

    protected function synchronizeSeriesEntries($seriesObj, $pos, $contents)
    {
        $lookup = $this->getDbTable('edition')
            ->select(['Series_ID' => $seriesObj->Series_ID, 'Position' => $pos]);
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
                Console::writeLine("FATAL: No match found for edition {$child['edition']->Edition_ID}");
                return false;
            }
        }
        return $result;
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
                foreach ($contents as $current) {
                    Console::writeLine("Possible match: " . $current['title']);
                }
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

    protected function fuzz($str)
    {
        $regex = '/[^a-z0-9]/';
        return preg_replace($regex, '', strtolower($str));
    }

    protected function fuzzyCompare($str1, $str2)
    {
        //Console::writeLine("Comparing {$str1} to {$str2}...");
        return $this->fuzz($str1) == $this->fuzz($str2);
    }

    protected function fuzzyContains($haystack, $needle)
    {
        return strstr($this->fuzz($haystack), $this->fuzz($needle));
    }

    protected function checkItemTitle($item, $title)
    {
        $itemTitle = (isset($item['Item_AltName']) && !empty($item['Item_AltName']))
            ? $item['Item_AltName'] : $item['Item_Name'];
        return $this->fuzzyCompare($title, $itemTitle);
    }

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
        return false;
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
}
