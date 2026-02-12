<?php

/**
 * Class to load information into the database.
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Ingest;

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Service\CityService;
use GeebyDeeby\Db\Service\EditionsCreditService;
use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\EditionsFullTextService;
use GeebyDeeby\Db\Service\EditionsOclcNumberService;
use GeebyDeeby\Db\Service\EditionsReleaseDateService;
use GeebyDeeby\Db\Service\ItemsAltTitleService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\ItemsTagService;
use GeebyDeeby\Db\Service\PeopleUriService;
use GeebyDeeby\Db\Service\PersonService;
use GeebyDeeby\Db\Service\PseudonymService;
use GeebyDeeby\Db\Service\PublisherService;
use GeebyDeeby\Db\Service\SeriesAltTitleService;
use GeebyDeeby\Db\Service\SeriesPublisherService;
use GeebyDeeby\Db\Service\TagService;
use GeebyDeeby\Db\Service\TagsUriService;

use function chr;
use function count;
use function in_array;
use function intval;
use function ord;
use function strlen;

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
    use \GeebyDeebyConsole\ConsoleOutputTrait;

    /**
     * Articles helper
     *
     * @var \GeebyDeeby\Articles
     */
    protected $articles;

    /**
     * Input interface
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * Question helper
     *
     * @var object
     */
    protected $questionHelper;

    /**
     * Cache for user-entered edition preferences.
     *
     * @var array
     */
    protected $editionPreferences = [];

    /**
     * Constructor
     *
     * @param \GeebyDeeby\Db\Service\PluginManager $services Database service manager
     * @param object                               $tables   Table plugin manager
     * @param \GeebyDeeby\Articles                 $articles Articles helper
     */
    public function __construct(\GeebyDeeby\Db\Service\PluginManager $services, $tables, $articles)
    {
        parent::__construct($services, $tables);
        $this->articles = $articles;
    }

    /**
     * Ingest data.
     *
     * @param array                                        $details Details from ModsExtractor or equivalent
     * @param EditionEntityInterface|SeriesEntityInterface $extra   Support object (Edition entity for existing,
     * Series entity for series)
     *
     * @return bool True for success
     */
    public function ingest(
        array $details,
        EditionEntityInterface|SeriesEntityInterface $extra
    ): bool {
        return ($extra instanceof EditionEntityInterface)
            ? $this->ingestExisting($details, $extra)
            : $this->ingestSeries($details, $extra);
    }

    /**
     * Display a prompt and read a single character.
     *
     * @param string $prompt Prompt to display
     *
     * @return string
     */
    protected function readchar($prompt)
    {
        readline_callback_handler_install(
            $prompt,
            function (): void {
            }
        );
        $char = stream_get_contents(STDIN, 1);
        readline_callback_handler_remove();
        return $char;
    }

    /**
     * Display a prompt and filter input to legal characters.
     *
     * @param string $prompt  Prompt to display
     * @param string $options Legal characters
     *
     * @return string
     */
    protected function getCharSelection($prompt, $options)
    {
        while (true) {
            $char = $this->readchar($prompt);
            if (in_array(strtoupper($char), str_split(strtoupper($options)))) {
                return $char;
            }
        }
    }

    /**
     * Prompt the user for a yes/no question, return true if YES selected.
     *
     * @param string $question Prompt to display.
     *
     * @return bool
     */
    public function askQuestion($question)
    {
        $char = $this->getCharSelection($question . ' (y/n) ', 'yn');
        $this->writeln('');
        return strtoupper($char) === 'Y';
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
        $childDetails = $this->addPersonDetails($childDetails);
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
            $this->writeln('FATAL: too many contents for single-part item.');
            return false;
        }
        if (!$this->checkItemTitles($item, $details['contents'][0])) {
            $this->writeln(
                "FATAL: Title mismatch '{$details['contents'][0]['title']}' vs. "
                . "'{$item['Item_Name']}' for item {$item['Item_ID']}."
            );
            return false;
        }
        $db = ['item' => $item, 'edition' => $editionObj->toArray()];
        $childDetails = [[$details['contents'][0], $db]];
        if (!($childDetails = $this->addPersonDetails($childDetails))) {
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
     * @param array                  $details    Details from ModsExtractor or equivalent
     * @param EditionEntityInterface $editionObj Edition row
     *
     * @return bool True for success
     */
    protected function ingestExisting(array $details, EditionEntityInterface $editionObj): bool
    {
        $series = $this->getSeriesForEdition($editionObj);
        $item = $this->getItemForEdition($editionObj);

        if (!$this->validateSeries($details, $editionObj, $series)) {
            $this->writeln('Series validation failed.');
            return false;
        }

        if ($item['Material_Type_ID'] == self::MATERIALTYPE_ISSUE) {
            return $this->ingestExistingIssue($details, $editionObj, $series);
        } elseif ($item['Material_Type_ID'] == self::MATERIALTYPE_WORK) {
            return $this->ingestExistingWork($details, $editionObj, $item, $series);
        }

        // If we got this far, we have bad data:
        $this->writeln(
            "FATAL: unexpected material type ID {$item['Material_Type_ID']}."
        );
        return false;
    }

    /**
     * Given a series string, extract a number.
     *
     * @param string $str Series string
     *
     * @return string
     */
    protected function getPositionFromSeriesString($str)
    {
        // Find the last number in the string.
        preg_match_all('/[0-9]+[,\s]*[0-9]*/', $str, $matches);
        $index = count($matches[0]) - 1;
        return isset($matches[0][$index])
            ? preg_replace('/[^0-9]/', '', $matches[0][$index]) : '0';
    }

    /**
     * Given a series object, get all titles (primary + alternate).
     *
     * @param SeriesEntityInterface $seriesObj Series object
     *
     * @return string[]
     */
    protected function getAllSeriesTitles(SeriesEntityInterface $seriesObj): array
    {
        $titles = [$seriesObj->getSeriesName()];
        $san = $this->getDbService(SeriesAltTitleService::class);
        foreach ($san->getAltTitles($seriesObj->getId()) as $alt) {
            $titles[] = $alt['Series_AltName'];
        }
        return $titles;
    }

    /**
     * Ingest series entry
     *
     * @param array                 $details   Details from ModsExtractor or equivalent
     * @param SeriesEntityInterface $seriesObj Series row
     *
     * @return bool True for success
     */
    protected function ingestSeries(array $details, SeriesEntityInterface $seriesObj): bool
    {
        $pos = empty($details['series'])
            ? 0
            : $this->getPositionFromSeriesString(current($details['series']));
        $item = ($pos === 0)
            ? $this->getItemForNewEdition($details['contents'][0]) : null;
        $contentSummary = array_map(
            fn ($n) =>  $n['title'],
            $details['contents']
        );
        $this->writeln('Working on ' . $seriesObj->getSeriesName() . " no. $pos...");
        $this->writeln(
            'Contents (' . count($contentSummary) . '): '
            . implode(' -- ', $contentSummary)
        );
        if (isset($details['publisher']['name'])) {
            $this->writeln('Publisher: ' . $details['publisher']['name']);
        }
        $this->editionPreferences = [];
        $childDetails = $this->synchronizeSeriesEntries($seriesObj, $pos, $details, $item);
        if (!$childDetails) {
            return false;
        }
        $childDetails = $this->addPersonDetails($childDetails);
        if (!$childDetails) {
            return false;
        }
        $details['contents'] = $childDetails;

        // Special case: multi-part work:
        if (count($childDetails) > 1) {
            $newChildData = [];
            $baseContainerTitle = trim(current(array_keys($details['series'])));
            // Check if the first child is named the same as the series; this is
            // probably a sign that it actually contains metadata about the
            // container, rather than one of its contents. We'll issue a warning so
            // this can be double-checked by hand, just in case.
            foreach ($this->getAllSeriesTitles($seriesObj) as $seriesTitle) {
                $foundMatch = $this->fuzzyCompare($seriesTitle, $childDetails[0][0]['title']);
                if ($foundMatch) {
                    $this->writeln(
                        'WARNING: assuming first child is top-level item due to '
                        . 'series title match.'
                    );
                    $baseContainerTitle = trim($childDetails[0][0]['title']);
                    $newChildData = array_shift($childDetails)[0];
                    break;
                }
            }
            $newChildData['title'] = $this->articles->articleAwareAppend(
                $baseContainerTitle,
                ' ' . trim(current($details['series']))
            );
            if (isset($details['url'])) {
                $newChildData['url'] = $details['url'];
            }
            $newEdition = $this->getChildIssueForSeries($seriesObj, $newChildData, $pos);
            if (!$newEdition || !$this->setTopLevelDetails($newEdition, $seriesObj, $details)) {
                return false;
            }
            return $this->updateChildWorks($newEdition, $childDetails);
        }
        // Standard case: single-part work:
        return $this->updateDatabaseForFlatEdition($seriesObj, $pos, $details);
    }

    /**
     * Set top-level details (publisher, ID number, links, dates, etc.) for container
     * item.
     *
     * @param EditionEntityInterface      $editionObj Edition row
     * @param array|SeriesEntityInterface $series     Series summary array (see getSeriesForEdition)
     * or Series row object
     * @param array                       $details    Details from ModsExtractor or equivalent
     *
     * @return bool True for success
     */
    protected function setTopLevelDetails(
        EditionEntityInterface $editionObj,
        array|SeriesEntityInterface $series,
        array $details
    ): bool {
        // Make sure we're operating on the top-level object:
        if ($parent = $editionObj->getParentEdition()) {
            $editionObj = $parent;
        }
        if (isset($details['date'])) {
            if (!$this->processDate($details['date'], $editionObj)) {
                return false;
            }
        }
        if (isset($details['publisher'])) {
            $pubResult = $this->processPublisher(
                $details['publisher'],
                $editionObj,
                $series['Series_ID']
            );
            if (!$pubResult) {
                return false;
            }
        }
        if (isset($details['notes'])) {
            if (!$this->processNotes($details['notes'], $editionObj)) {
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
     * through synchronizeSeriesEntries() and addPersonDetails().
     *
     * @return bool True for success
     */
    protected function updateDatabaseForFlatEdition(SeriesEntityInterface $series, int $pos, array $details): bool
    {
        [$data, $db] = $details['contents'][0];
        $data['subjects'] = $details['subjects'] ?? [];
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
     * through synchronizeChildren() and addPersonDetails().
     *
     * @return bool True for success
     */
    protected function updateDatabaseForHierarchicalEdition(
        $editionObj,
        $series,
        $details
    ) {
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
            $year = $parts[2] ?? null;
            $month = $parts[0] ?? null;
            $day = $parts[1] ?? null;
        } else {
            $year = $parts[0] ?? null;
            $month = $parts[1] ?? null;
            $day = $parts[2] ?? null;
        }
        return [$year, $month, $day];
    }

    /**
     * Given a date, update the edition.
     *
     * @param string                 $date       Date string.
     * @param EditionEntityInterface $editionObj Row representing Edition row in database.
     *
     * @return bool Success?
     */
    protected function processDate(string $date, EditionEntityInterface $editionObj): bool
    {
        [$year, $month, $day] = $this->parseDate($date);
        $service = $this->getDbService(EditionsReleaseDateService::class);
        $known = $service->getDatesForEdition($editionObj->getId());
        $foundMatch = false;
        $current = false;
        foreach ($known as $current) {
            if (
                ($current->getMonth() == $month || null === $month)
                && $current->getYear() == $year
                && ($current->getDay() == $day || null === $day)
            ) {
                $foundMatch = true;
                break;
            }
        }
        if (!$foundMatch && count($known) > 0) {
            $this->writeln(
                "FATAL: Unexpected date value in database; expected $date."
            );
            return false;
        }
        if (
            ($current && $current->getMonth() > 0 && null === $month)
            || ($current && $current->getDay() > 0 && null === $day)
        ) {
            $this->writeln(
                'WARNING: More specific date in database than in incoming data.'
            );
        }
        if (count($known) == 0) {
            $this->writeln("Adding date: {$date}");
            $entity = $service->createEntity()
                ->setEdition($editionObj)
                ->setYear($year)
                ->setMonth(empty($month) ? 0 : $month)
                ->setDay(empty($day) ? 0 : $day);
            $service->persistEntity($entity);
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
                [' st.', ' w.', '23rd', 'no. ', '&', 'ave.', ' n. ', ' e. ', ' w. '],
                [' street', ' west', '23d', '', 'and', 'avenue', ' north ', ' east ', ' west '],
                strtolower($street)
            ),
            '.'
        );
    }

    /**
     * Normalize a street string for comparison purposes.
     *
     * @param string $street1 First street to compare
     * @param string $street2 Second street to compare
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
        // trim leading article
        $pub = preg_replace('/^The /i', '', $pub);
        // normalize abbreviations
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
     * @param string $rawCity String to normalize.
     *
     * @return string
     */
    protected function normalizeCity($rawCity)
    {
        [$city] = explode('(', $rawCity);
        return str_replace(', n.y', '', strtolower(trim($city)));
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
     * @param array                  $publisher  Publisher data (array with name/place keys) from metadata.
     * @param EditionEntityInterface $editionObj Edition row
     * @param int                    $seriesId   ID of series being worked on.
     *
     * @return bool True on success.
     */
    protected function processPublisher(array $publisher, EditionEntityInterface $editionObj, int $seriesId): bool
    {
        [$name, $street] = $this->separateNameAndStreet($publisher['name']);
        if (empty($street)) {
            $this->writeln('WARNING: No street address; skipping publisher.');
            return true;
        }
        $place = $publisher['place'];
        $cityService = $this->getDbService(CityService::class);
        $pubService = $this->getDbService(PublisherService::class);
        $result = $this->getDbService(SeriesPublisherService::class)->getPublishersForSeries($seriesId);
        $match = false;
        foreach ($result as $current) {
            $city = $current['City_ID']
                ? $cityService->getByPrimaryKey($current['City_ID']) : null;
            $pub = $current['Publisher_ID']
                ? $pubService->getByPrimaryKey($current['Publisher_ID']) : null;
            if (
                $city && $this->citiesMatch($place, $city->getCityName())
                && $pub && $this->publishersMatch($name, $pub->getPublisherName())
                && $this->streetsMatch($street, $current['Street'])
            ) {
                $match = $current['Series_Publisher_ID'];
                break;
            }
        }
        if (!$match) {
            $this->writeln(
                "WARNING: No series/publisher match for $name, $street, $place; "
                . 'skipping publisher.'
            );
            return true;
        }
        $preferredPublisherId = $editionObj->getPreferredPublisher()?->getId();
        if ($preferredPublisherId && $preferredPublisherId != $match) {
            $edPublishers = $this->getDbService(EditionService::class)
                ->getPublishersForEdition($editionObj->getId());
            foreach ($edPublishers as $ed) {
                // fast-forward to end of results...
            }
            $this->writeln('Publisher mismatch in edition.');
            $this->writeln(
                isset($ed) ? "Old: {$ed['Publisher_Name']}, {$ed['Street']}, {$ed['City_Name']}" : 'Old: none'
            );
            $this->writeln("New: $name, $street, $place");
            if (!$this->askQuestion('Change?')) {
                $this->writeln('FATAL: Aborting ingest due to publisher mismatch.');
                return false;
            }
        }
        if ($preferredPublisherId && $preferredPublisherId == $match) {
            return true;
        }
        $this->writeln("Updating address to $name, $street, $place");
        $editionObj->setPreferredPublisher($match);
        $this->getDbService(EditionService::class)->persistEntity($editionObj);
        return true;
    }

    /**
     * Normalize and store edition notes.
     *
     * @param array                  $notes      Notes
     * @param EditionEntityInterface $editionObj Edition row
     *
     * @return bool True on success
     */
    protected function processNotes(array $notes, EditionEntityInterface $editionObj): bool
    {
        $notes = trim(implode(' ', $notes));
        if (!$editionObj->getDescription()) {
            $this->writeln("Adding edition description: $notes");
            $editionObj->setDescription($notes);
            $this->getDbService(EditionService::class)->persistEntity($editionObj);
            return true;
        }
        if (false === strstr($editionObj->getDescription(), $notes)) {
            $this->writeln(
                'WARNING: edition description mismatch: '
                . "{$notes} vs. {$editionObj->getDescription()}"
            );
        }
        return true;
    }

    /**
     * Normalize and store OCLC numbers.
     *
     * @param array                  $oclc       Incoming OCLC numbers
     * @param EditionEntityInterface $editionObj Edition row
     *
     * @return bool True on success
     */
    protected function processOclcNum(array $oclc, EditionEntityInterface $editionObj): bool
    {
        // strip off non-digits (useless OCLC prefixes):
        foreach ($oclc as $i => $current) {
            $oclc[$i] = preg_replace('/[^0-9]/', '', $current);
        }
        $service = $this->getDbService(EditionsOclcNumberService::class);
        $known = $service->getOCLCNumbersForEdition($editionObj->getId());
        $knownArr = [];
        foreach ($known as $current) {
            $knownArr[] = $current->getOclcNumber();
        }
        foreach (array_diff($oclc, $knownArr) as $current) {
            $this->writeln("Adding OCLC number: {$current}");
            $entity = $service->createEntity()->setEdition($editionObj)->setOclcNumber($current);
            $service->persistEntity($entity);
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
        } elseif (strstr($url, 'usf.edu')) {
            return self::FULLTEXT_SOURCE_USF;
        } elseif (strstr($url, 'stanford.edu')) {
            return self::FULLTEXT_SOURCE_STANFORD;
        }
        return null;
    }

    /**
     * Validate and store URLs for edition.
     *
     * @param array                  $urls       Incoming URLs
     * @param EditionEntityInterface $editionObj Edition row
     *
     * @return bool True on success
     */
    protected function processUrl(array $urls, EditionEntityInterface $editionObj): bool
    {
        $service = $this->getDbService(EditionsFullTextService::class);
        $known = $service->getFullTextForEdition($editionObj->getId());
        $knownArr = array_map(fn ($current) => $current->getUrl(), $known);
        foreach (array_diff($urls, $knownArr) as $current) {
            $source = $this->getSourceForUrl($current);
            if (null === $source) {
                $this->writeln('FATAL: Unexpected URL: ' . $current);
                return false;
            }
            $this->writeln("Adding URL: {$current}");
            $entity = $service->createEntity()
                ->setEdition($editionObj)
                ->setUrl($current)
                ->setFullTextSource($source);
            $service->persistEntity($entity);
        }
        return true;
    }

    /**
     * Update the database with person details; return true on success.
     *
     * @param array  $ids      Array of person IDs.
     * @param array  $db       Data from the database.
     * @param string $idKey    Key in $db containing relevant person IDs
     * @param string $roleName Descriptive name of role being processed
     * @param int    $roleId   Identifier of role being processed
     *
     * @return bool
     */
    protected function processPeople($ids, $db, $idKey, $roleName, $roleId)
    {
        if ($this->hasPersonProblem($ids, $db[$idKey], $roleName)) {
            return false;
        }
        $service = $this->getDbService(EditionsCreditService::class);
        foreach (array_diff($ids, $db[$idKey]) as $current) {
            $this->writeln("Attaching $roleName ID $current");
            $entity = $service->createEntity()
                ->setEdition($db['edition']->getId())
                ->setPerson($current)
                ->setRole($roleId);
            $service->persistEntity($entity);
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
        return isset($parts[1]) ? intval($parts[1]) : false;
    }

    /**
     * Given an array of uri => text, produce an array of database tag IDs.
     *
     * @param array $subjects Associative array of subject data.
     *
     * @return array|false
     */
    protected function subjectUrisToIds(array $subjects): array|false
    {
        $tagsUris = $this->getDbService(TagsUriService::class);
        $tags = $this->getDbService(TagService::class);

        $ids = [];
        foreach ($subjects as $uri => $text) {
            if ($tagId = $this->extractIdFromDimeNovelsUri($uri, 'Tag')) {
                $exactMatch = $tags->getByPrimaryKey($tagId);
                if (!$this->fuzzyCompare($text, $exactMatch->getTag())) {
                    $this->writeln("FATAL: Tag mismatch: $uri, '$text'");
                    return false;
                }
                $id = $exactMatch->getId();
            } else {
                $uriLookup = $tagsUris->getTagsForURI($uri);
                if (count($uriLookup) == 0) {
                    $uriLookup = $tagsUris->getTagsForURI($this->switchProtocol($uri));
                }
                $id = $uriLookup[0]['Tag_ID'] ?? null;
            }
            if ($id) {
                $ids[$uri] = $id;
            } else {
                if (!stristr($uri, 'loc.gov')) {
                    $this->writeln('FATAL: Unexpected subject URI: ' . $uri);
                    return false;
                }
                if ($entity = $tags->getByLabel($text)) {
                    $this->writeln("Upgrading subject: $text");
                    $entity->setTagType(self::TAGTYPE_LC);
                } else {
                    $this->writeln("Adding subject: $text");
                    $entity = $tags->createEntity()->setTag($text)->setTagType(self::TAGTYPE_LC);
                }
                $tags->persistEntity($entity);
                $ids[$uri] = $entity->getId();
                $link = $tagsUris->createEntity()
                    ->setTag($ids[$uri])
                    ->setUri($uri)
                    ->setPredicate(self::PREDICATE_OWL_SAMEAS);
                $tagsUris->persistEntity($link);
            }
        }
        return $ids;
    }

    /**
     * Update the database with alternate title data. Return true on success.
     *
     * @param string $title     Primary title
     * @param array  $altTitles Alternate titles
     * @param array  $db        Data from the database.
     *
     * @return bool
     */
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
                $formattedNeedle
                    = $articleHelper->formatTrailingArticles($currentNeedle);
                if (
                    $this->fuzzyContains($currentHaystack, $currentNeedle)
                    || $this->fuzzyContains($currentHaystack, $formattedNeedle)
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
            $service = $this->getDbService(ItemsAltTitleService::class);
            $result = $service->getAltTitles($item);
            $existing = [$db['item']['Item_Name']];
            foreach ($result as $current) {
                $existing[] = $current['Item_AltName'];
            }
            foreach ($filteredTitles as $newTitle) {
                $skip = false;
                foreach ($existing as $current) {
                    if (
                        $this->fuzzyCompare($newTitle, $current)
                        || $this->fuzzyContains($current, $newTitle)
                    ) {
                        $skip = true;
                        break;
                    }
                }
                if (!$skip) {
                    $new = $service->createEntity()->setItem($item)->setAltName($newTitle);
                    $service->persistEntity($new);
                    $this->writeln('Added alternate title: ' . $newTitle);
                }
            }
        }
        return true;
    }

    /**
     * Write subject data to the database. Return true on success.
     *
     * @param array $subjects Associative array of subject data.
     * @param array $db       Data from the database.
     *
     * @return bool
     */
    protected function processSubjects(array $subjects, array $db): bool
    {
        $item = $db['item']['Item_ID'];
        $subjectIds = $this->subjectUrisToIds($subjects);
        if (false === $subjectIds) {
            return false;
        }
        $itemsTags = $this->getDbService(ItemsTagService::class);
        $existingTags = $itemsTags->getTagsForItem($item);
        $existingIds = array_map(fn ($tag) => $tag['Tag_ID'], $existingTags);
        $missing = array_unique(array_diff($subjectIds, $existingIds));
        if (count($missing) > 0) {
            $this->writeln('Adding subject IDs: ' . implode(', ', $missing));
            foreach ($missing as $id) {
                $link = $itemsTags->createEntity()->setItem($item)->setTag($id);
                $itemsTags->persistEntity($link);
            }
        }
        return true;
    }

    /**
     * Given an edition object, update its child works with the provided details.
     * Return true on success, false on error.
     *
     * @param EditionEntityInterface $editionObj Edition object
     * @param array                  $details    Incoming details
     *
     * @return bool
     */
    protected function updateChildWorks(EditionEntityInterface $editionObj, array $details): bool
    {
        foreach ($details as $i => $current) {
            [$data, $db] = $current;
            if (!$db) {
                if (!$this->addChildWorkToEdition($editionObj, $data, $i)) {
                    return false;
                }
            } else {
                $this->writeln(
                    "Processing edition ID {$db['edition']->getId()}"
                );
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
     * @param int       $item  Item ID
     * @param int|int[] $roles Role ID(s) to accept
     *
     * @return array
     */
    protected function getPeopleForItem(int $item, int|array $roles = self::ROLE_AUTHOR): array
    {
        $service = $this->getDbService(EditionsCreditService::class);
        $ids = [];
        foreach ($service->getCreditsForItem($item) as $credit) {
            if (in_array($credit['Role_ID'], (array)$roles)) {
                $ids[$credit['Person_ID']] = trim($credit['First_Name'] . ' ' . $credit['Last_Name']);
            }
        }
        return $ids;
    }

    /**
     * Support method for getItemMatchCandidatesUsingTitle() -- given raw data and
     * query results, score the results.
     *
     * @param array  $data       Raw data
     * @param array  $options    Query results
     * @param string $titleField Name of field to examine for title
     *
     * @return array
     */
    protected function evaluateItemMatchTitleCandidates(
        array $data,
        array $options,
        string $titleField = 'Item_Name'
    ): array {
        $candidates = [];
        foreach ($options as $current) {
            $confidence = 100;
            $currentCredits = $this->getPeopleForItem(
                $current['Item_ID'],
                [self::ROLE_AUTHOR, self::ROLE_EDITOR]
            );
            $authorsAndEditors = array_merge(
                $data['authorIds'] ?? [],
                $data['editorIds'] ?? []
            );
            // Distrust unattributed items by default:
            if (count($authorsAndEditors) === 0) {
                $confidence -= 10;
            }
            $diffCount = count(
                array_diff($authorsAndEditors, array_keys($currentCredits))
            );
            if ($diffCount != 0) {
                $confidence -= 20;
            }
            $intersectCount = count(
                array_intersect($authorsAndEditors, array_keys($currentCredits))
            );
            if ($intersectCount != count($authorsAndEditors)) {
                $confidence -= 20;
            }
            if (!$this->fuzzyCompare($data['title'], $current[$titleField])) {
                $confidence -= $this->hasMatchingAltTitle(
                    $data['title'],
                    $current['Item_ID'],
                    $current['Item_Name'] ?? ''
                ) ? 10 : 25;
            }
            $candidates[] = [
                'id' => $current['Item_ID'],
                'title' => $current[$titleField],
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
        $service = $this->getDbService(ItemService::class);
        // Start by searching for full title; we'll break it down into chunks as we
        // go...
        $pos = strlen($data['title']);
        do {
            $strippedTitle = substr($data['title'], 0, $pos);
            // check to see if we have a title match
            $options = $service->getSuggestions($strippedTitle);
            $candidates = $this->evaluateItemMatchTitleCandidates($data, $options);
            $commaPos = strrpos($strippedTitle, ',');
            $semiPos = strrpos($strippedTitle, ';');
            $pos = $commaPos > $semiPos ? $commaPos : $semiPos;
        } while (count($candidates) == 0 && $pos > 0);
        return $candidates;
    }

    /**
     * Reduce a string to alphanumeric chunks
     *
     * @param string $str String to process
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
        $ec = $this->getDbService(EditionsCreditService::class);
        $candidates = [];
        foreach ($ec->getItemCreditsForPerson($author, 'title', false) as $current) {
            $score = $this->measureStringSimilarity(
                $current['Item_Name'],
                $data['title']
            );
            if ($score > 0) {
                $currentCredits = $this->getPeopleForItem($current['Item_ID']);
                $candidates[] = [
                    'id' => $current['Item_ID'],
                    'title' => $current['Item_Name'],
                    'authors' => implode(', ', $currentCredits),
                    'confidence' => $score,
                ];
            } else {
                $service = $this->getDbService(ItemsAltTitleService::class);
                foreach ($service->getAltTitles($current['Item_ID']) as $currentAlt) {
                    $score = $this->measureStringSimilarity(
                        $currentAlt['Item_AltName'],
                        $data['title']
                    );
                    if ($score > 0) {
                        $currentCredits = $this->getPeopleForItem($current['Item_ID']);
                        $candidates[] = [
                            'id' => $current['Item_ID'],
                            'title' => $currentAlt['Item_AltName'] . ' (alt. title for ' . $current['Item_Name'] . ')',
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
        $allAuthors = [];
        $pseudo = $this->getDbService(PseudonymService::class);
        foreach ($data['authorIds'] as $author) {
            $allAuthors[] = $author;
            foreach ($pseudo->getPseudonyms($author) as $p) {
                $allAuthors[] = $p['Person_ID'];
            }
            foreach ($pseudo->getRealNames($author) as $p) {
                $allAuthors[] = $p['Person_ID'];
            }
        }
        foreach (array_unique($allAuthors) as $author) {
            $candidates = array_merge(
                $candidates,
                $this->getItemMatchCandidatesUsingAuthor($author, $data)
            );
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
            if (
                !isset($new[$current['id']])
                || $new[$current['id']]['confidence'] < $current['confidence']
            ) {
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

    /**
     * Add an alt title to an item.
     *
     * @param string $title Alt title
     * @param string $item  Item ID
     *
     * @return void
     */
    protected function addAltTitle($title, $item)
    {
        $service = $this->getDbService(ItemsAltTitleService::class);
        $entity = $service->createEntity()->setItem($item)->setAltName($title);
        $service->persistEntity($entity);
    }

    /**
     * Create an Item record for the provided data array.
     *
     * @param array $data Incoming data, with 'title' and (optional) 'authorIds'
     * keys.
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
            foreach ($data['authors'] ?? [] as $current) {
                $authors[] = $current['name'];
            }
            $authors = count($authors) > 0
                ? 'by ' . implode(', ', $authors) : ' - no credits';
            $this->writeln(
                "Found candidate(s) for match with {$data['title']} $authors\n"
            );
            $options = '0';
            foreach ($candidates as $i => $current) {
                if ($i > 25) {
                    $this->writeln('...and more options than can be shown!');
                    break;
                }
                $options .= chr(65 + $i);
                $authorList = !empty($current['authors'])
                    ? ' by ' . $current['authors'] : ' - no credits';
                $this->writeln(
                    chr(65 + $i) . '. ' . $current['title']
                    . $authorList . ' [ID = ' . $current['id'] . ']'
                    . ' (confidence: ' . $current['confidence'] . '%)'
                );
            }
            $this->writeln("\n0. NONE OF THE ABOVE -- CREATE NEW ITEM.");
            $char = $this->getCharSelection("\nPlease select one: ", $options);
            if ($char !== '0') {
                $response = ord(strtoupper($char)) - 65;
                $fuzzyTitleCompare = $this->fuzzyCompare(
                    $data['title'],
                    $candidates[$response]['title']
                );
                $altTitleCompare = $this->hasMatchingAltTitle(
                    $data['title'],
                    $candidates[$response]['id'],
                    $candidates[$response]['title']
                );
                if (!$fuzzyTitleCompare && !$altTitleCompare) {
                    $this->addAltTitle($data['title'], $candidates[$response]['id']);
                }
                return $candidates[$response]['id'];
            }
        }

        // If we made it this far, we need to create a new item.
        $service = $this->getDbService(ItemService::class);
        $entity = $service->createEntity()->setItemName($data['title'])->setMaterialType($type);
        $service->persistEntity($entity);
        $id = $entity->getId();
        $this->writeln("Added item ID {$id} ({$data['title']})");
        return $id;
    }

    /**
     * Add a child work to an edition
     *
     * @param EditionEntityInterface $parentEdition Parent edition object
     * @param array                  $data          Data representing child edition
     * @param int                    $pos           Position of child within parent
     *
     * @return object|bool Edition object on success, false otherwise.
     */
    protected function addChildWorkToEdition(
        EditionEntityInterface $parentEdition,
        array $data,
        int $pos = 0
    ): EditionEntityInterface|false {
        $item = $this->getItemForNewEdition($data);
        $edName = $parentEdition->getEditionName();
        $series = $parentEdition->getSeries();
        $service = $this->getDbService(EditionService::class);
        $altName = $this->hasMatchingAltTitle($data['title'], $item, '', false, true);
        $newObj = $service->createEntity()
            ->setEditionName($edName)
            ->setSeries($series)
            ->setItem($item)
            ->setParentEdition($parentEdition)
            ->setPositionInParent($pos)
            ->setPreferredItemAlternateTitle($altName ?: null);
        $service->persistEntity($newObj);
        $this->writeln('Added edition ID ' . $newObj->getId());
        return $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $newObj,
                'authorIds' => [],
                'editorIds' => [],
                'item' => $this->getItemForEdition($newObj),
            ]
        );
    }

    /**
     * Create an edition object in a series.
     *
     * @param SeriesEntityInterface $series Series row object
     * @param int                   $item   Item ID number
     * @param int                   $pos    Position of edition in series
     * @param array                 $data   Incoming data
     *
     * @return EditionEntityInterface Edition row object
     */
    protected function createEditionInSeries(
        SeriesEntityInterface $series,
        int $item,
        int $pos,
        array $data
    ): EditionEntityInterface {
        $edName = $this->articles->articleAwareAppend($series->getSeriesName(), ' edition');
        $editionService = $this->getDbService(EditionService::class);
        $altName = $this->hasMatchingAltTitle($data['title'], $item, '', false, true);
        $edition = $editionService->createEntity()
            ->setEditionName($edName)
            ->setSeries($series)
            ->setItem($item)
            ->setPosition($pos)
            ->setPreferredItemAlternateTitle($altName ?: null);
        $editionService->persistEntity($edition);
        return $edition;
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
        $this->writeln('Added edition ID ' . $newObj->getId());
        return $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $newObj,
                'authorIds' => [],
                'editorIds' => [],
                'item' => $this->getItemForEdition($newObj),
            ]
        );
    }

    /**
     * Create or load an issue in the specified series position.
     *
     * @param object $series Series row object.
     * @param array  $data   Incoming data, with 'title' and (optional) 'authorIds'
     * keys.
     * @param int    $pos    Position of issue in series.
     *
     * @return object|bool New edition object (false on error)
     */
    protected function getChildIssueForSeries($series, $data, $pos = 0)
    {
        // first make sure we don't already have an issue:
        $lookup = $this->getDbService(EditionService::class)->getBySeriesAndPosition($series, $pos);
        if (count($lookup) == 0) {
            $item = $this->getItemForNewEdition($data, self::MATERIALTYPE_ISSUE);
            $newObj = $this->createEditionInSeries($series, $item, $pos, $data);
            $this->writeln('Added edition ID ' . $newObj->getId());
            $edition = $newObj;
        } elseif (count($lookup) == 1) {
            $edition = null;
            foreach ($lookup as $current) {
                $edition = $current;
            }
        } else {
            $edition = $this->pickEdition($lookup, $data['url'] ?? []);
        }
        return $this->updateWorkInDatabase(
            $data,
            [
                'edition' => $edition,
                'authorIds' => [],
                'editorIds' => [],
                'item' => $this->getItemForEdition($edition),
            ]
        );
    }

    /**
     * Check for a match between an incoming title and an alternate title of an Item.
     *
     * @param string $title     Incoming title to check
     * @param int    $itemID    Item to check against
     * @param string $itemTitle Title of item to check against (for additional fuzzy
     * matching)
     * @param bool   $warn      Should we display a warning if we find a match?
     * @param bool   $returnId  Should we return the alt title sequence ID instead of
     * boolean true?
     *
     * @return bool|int
     */
    protected function hasMatchingAltTitle(
        $title,
        $itemID,
        $itemTitle,
        $warn = false,
        $returnId = false
    ) {
        // Check the alt titles table:
        $service = $this->getDbService(ItemsAltTitleService::class);
        foreach ($service->getAltTitles($itemID) as $current) {
            $currentAlt = $current['Item_AltName'];
            if ($this->fuzzyCompare($title, $currentAlt)) {
                if ($warn) {
                    $this->writeln(
                        'WARNING: Found match in alt rather than primary title: '
                        . $currentAlt
                    );
                }
                return $returnId ? $current['Sequence_ID'] : true;
            }
        }

        // Check for partial matches of an "A; or, B" type of title; note that we
        // can't do this in "return ID" mode because these kinds of alt. title
        // match do not exist in the database table and thus have no sequence ID
        // to return.
        [$itemArticle, $itemMainTitle] = $this->articles->separateArticle($itemTitle);
        $titleParts = preg_split('/[;:, ]\s*or[;:, ]/', $itemMainTitle);
        if (!$returnId && count($titleParts) > 1) {
            if ($itemArticle) {
                $titleParts[0] .= ', ' . trim($itemArticle);
            }
            foreach ($titleParts as $part) {
                if ($this->fuzzyCompare($title, $part)) {
                    if ($warn) {
                        $this->writeln(
                            'WARNING: Partial title match only: ' . $part
                        );
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Process a title.
     *
     * @param string $title Title to process.
     * @param array  $db    Data to check against.
     *
     * @return bool
     */
    protected function processTitle($title, $db)
    {
        if ($this->checkItemTitle($db['item'], $title)) {
            return true;
        }
        $hasMatchingAlt = $this->hasMatchingAltTitle(
            $title,
            $db['item']['Item_ID'],
            $db['item']['Item_Name'],
            true
        );
        if ($hasMatchingAlt) {
            return true;
        }
        $this->writeln('FATAL: Unexpected title mismatch.');
        $this->writeln('Incoming: ' . $title);
        $dbTitle = $db['item']['Item_AltName']
            ?? $db['item']['Item_Name'];
        $this->writeln('Database: ' . $dbTitle);
        return false;
    }

    /**
     * Process extent data
     *
     * @param string                 $extent Extent to process.
     * @param EditionEntityInterface $ed     Edition entity to check against.
     *
     * @return bool
     */
    protected function processExtent(string $extent, EditionEntityInterface $ed): bool
    {
        $existingExtent = $ed->getExtentInParent();
        if (!empty($existingExtent) && $existingExtent !== $extent) {
            $this->writeln(
                'WARNING: Unexpected extent: ' . $extent . '; Expected: ' . $existingExtent
            );
            return true;
        }
        if (!$ed->getParentEdition()) {
            $this->writeln('FATAL ERROR: Missing parent ID.');
            return false;
        }
        if (empty($existingExtent)) {
            $this->writeln('Adding extent: ' . $extent);
            $ed->setExtentInParent($extent);
            $this->getDbService(EditionService::class)->persistEntity($ed);
        }
        return true;
    }

    /**
     * Update a work in the database.
     *
     * @param array $data Incoming data about a single work.
     * @param array $db   Existing data about the same work.
     *
     * @return EditionEntityInterface|false Edition object on success, false otherwise.
     */
    protected function updateWorkInDatabase(array $data, array $db): EditionEntityInterface|false
    {
        if (isset($data['title']) && !$this->processTitle($data['title'], $db)) {
            return false;
        }
        if (isset($data['altTitles'])) {
            if (!$this->processAltTitles($data['title'], $data['altTitles'], $db)) {
                return false;
            }
        }
        if (isset($data['extent']) && !empty($data['extent'])) {
            if (!$this->processExtent($data['extent'], $db['edition'])) {
                return false;
            }
        }
        if (isset($data['authorIds'])) {
            $authorResult = $this->processPeople(
                $data['authorIds'],
                $db,
                'authorIds',
                'author',
                self::ROLE_AUTHOR
            );
            if (!$authorResult) {
                return false;
            }
        }
        if (isset($data['editorIds'])) {
            $editorResult = $this->processPeople(
                $data['editorIds'],
                $db,
                'editorIds',
                'editor',
                self::ROLE_EDITOR
            );
            if (!$editorResult) {
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
     * Given first and last names, try to find a matching person.
     *
     * @param string $first    First name
     * @param string $last     Last name
     * @param string $raw      Raw name string
     * @param int[]  $expected IDs we're expecting to find (if any)
     *
     * @return int|false
     */
    protected function fuzzyPersonMatch(string $first, string $last, string $raw, array $expected): int|false
    {
        $people = [];
        $options = '';
        $result = $this->getDbService(PersonService::class)->getFuzzyMatches($first, $last);
        if (count($result) === 0) {
            return false;
        }
        foreach ($result as $option) {
            if (in_array($option->getId(), $expected)) {
                $this->writeln(
                    "WARNING: Fuzzy person match between $raw and "
                    . $option->getFirstName() . ' ' . $option->getLastName()
                    . $option->getExtraDetails()
                );
                return $option->getId();
            }
        }
        $this->writeln("Possible matches found for $raw...");
        foreach ($result as $option) {
            $people[] = $option->getId();
            $letter = chr(64 + count($people));
            $options .= $letter;
            $this->writeln(
                $letter . '. ' . $option->getFirstName() . ' ' . $option->getLastName()
                . $option->getExtraDetails()
            );
        }
        $char = strtoupper(
            $this->getCharSelection("\nPlease select one: ", $options)
        );
        return $people[ord($char) - 65];
    }

    /**
     * Given a name string, look up a matching person ID.
     *
     * @param string $str      Name string
     * @param int[]  $expected Person IDs we're expecting to find (from database,
     * if we're trying to match an existing entry)
     *
     * @return int|false Person ID, or false for no match.
     */
    protected function getPersonIdForString(string $str, array $expected = []): int|false
    {
        $bad = '(dime novelist)';
        if (substr(strtolower($str), -strlen($bad)) === $bad) {
            $str = trim(substr($str, 0, strlen($str) - strlen($bad)));
        }
        $parts = explode(',', $str, 3);
        $last = trim($parts[0]);
        $first = isset($parts[1]) ? trim($parts[1]) : '';
        // handle special case -- last name with date, no first:
        if (preg_match('/\d{4}\\??-\d{4}\\??/', $first)) {
            $parts[2] = $first;
            $first = null;
        }
        $extra = isset($parts[2]) ? (', ' . trim($parts[2])) : '';
        if ($exact = $this->getDbService(PersonService::class)->getExactMatch($first, $last, $extra)) {
            return $exact->getId();
        }
        return $this->fuzzyPersonMatch($first, $last, $str, $expected);
    }

    /**
     * Update data with author details from the database.
     *
     * @param array $details Collected details (will be returned in updated form)
     *
     * @return array
     */
    protected function addPersonDetails($details)
    {
        foreach ($details as & $match) {
            if ($match[1]) {
                $credits = $this->getDbService(EditionsCreditService::class)
                    ->getCreditsForEdition($match[1]['edition']['Edition_ID']);
                $match[1]['authorIds'] = $match[1]['editorIds'] = [];
                foreach ($credits as $credit) {
                    if ($credit['Role_ID'] == self::ROLE_AUTHOR) {
                        $match[1]['authorIds'][] = $credit['Person_ID'];
                    }
                    if ($credit['Role_ID'] == self::ROLE_EDITOR) {
                        $match[1]['editorIds'][] = $credit['Person_ID'];
                    }
                }
            }
            $match[0]['authorIds'] = $match[0]['editorIds'] = [];
            foreach ($match[0]['authors'] ?? [] as $current) {
                $id = $this->getPersonId($current, $match[1]['authorIds'] ?? []);
                if ($id === false) {
                    return false;
                }
                $match[0]['authorIds'][] = $id;
            }
            foreach ($match[0]['editors'] ?? [] as $current) {
                $id = $this->getPersonId($current, $match[1]['editorIds'] ?? []);
                if ($id === false) {
                    return false;
                }
                $match[0]['editorIds'][] = $id;
            }
        }
        return $details;
    }

    /**
     * Check whether there is a person inconsistency.
     *
     * @param array  $incomingList Author list from incoming data
     * @param array  $storedList   Author list from existing record
     * @param string $roleName     The role being checked
     *
     * @return bool
     */
    protected function hasPersonProblem($incomingList, $storedList, $roleName)
    {
        $unexpected = array_diff($storedList, $incomingList);
        if (count($unexpected) > 0) {
            $pseudo = $this->getDbService(PseudonymService::class);
            $stillUnexpected = [];
            foreach ($unexpected as $current) {
                $matched = false;
                $pseudonyms = $pseudo->getPseudonyms($current);
                foreach ($pseudonyms as $p) {
                    if (in_array($p['Pseudo_Person_ID'], $incomingList)) {
                        $matched = true;
                        $this->writeln(
                            'WARNING: Database contains person ' . $current
                            . ' but incoming data uses pseudonym '
                            . $p['Pseudo_Person_ID']
                        );
                        break;
                    }
                }
                $real = $pseudo->getRealNames($current);
                foreach ($real as $r) {
                    if (in_array($r['Real_Person_ID'], $incomingList)) {
                        $matched = true;
                        $this->writeln(
                            'WARNING: Database contains person ' . $current
                            . ' but incoming data uses real name '
                            . $r['Real_Person_ID']
                        );
                        break;
                    }
                    $realPseudos = $pseudo->getPseudonyms($r['Real_Person_ID']);
                    foreach ($realPseudos as $realPseudo) {
                        $matched = in_array(
                            $realPseudo['Pseudo_Person_ID'],
                            $incomingList
                        );
                        if ($matched) {
                            $this->writeln(
                                'WARNING: Database contains person ' . $current
                                . ' but incoming data uses alternate pseudonym '
                                . $realPseudo['Pseudo_Person_ID']
                            );
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
                    $this->writeln(
                        "WARNING: no incoming {$roleName}s, but {$roleName}s "
                        . 'found in database.'
                    );
                } else {
                    $this->writeln(
                        "Found unexpected $roleName ID(s) in database: "
                        . implode(', ', $unexpected)
                    );
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Given a URI, switch the protocol from HTTP to HTTPS (or vice versa).
     *
     * @param string $uri URI to switch
     *
     * @return string
     */
    protected function switchProtocol($uri)
    {
        return str_replace(
            ['http://', 'https://'],
            ['https://', 'http://'],
            $uri
        );
    }

    /**
     * Given a URI, look up a matching person ID.
     *
     * @param string $uri URI
     *
     * @return int|bool Person ID, or false for no match.
     */
    protected function getPersonIdForUri(string $uri): int|bool
    {
        if (!($id = $this->extractIdFromDimeNovelsUri($uri, 'Person'))) {
            $service = $this->getDbService(PeopleUriService::class);
            $result = $service->getPeopleForURI($uri);
            if (count($result) === 0) {
                $result = $service->getPeopleForURI($this->switchProtocol($uri));
            }
            foreach ($result as $curr) {
                $id = $curr['Person_ID'];
            }
        }
        return $id;
    }

    /**
     * Get a person ID for extracted person data.
     *
     * @param array $current  Person data
     * @param array $expected Expected person IDs
     *
     * @return int
     */
    protected function getPersonId($current, $expected)
    {
        if (isset($current['uri'])) {
            $id = $this->getPersonIdForUri($current['uri']);
        } else {
            $this->writeln(
                "WARNING: Missing URI for {$current['name']}..."
            );
            $id = $this
                ->getPersonIdForString($current['name'], $expected);
        }
        if (!$id) {
            $text = isset($current['uri'])
                ? $current['uri'] . ' (' . $current['name'] . ')'
                : $current['name'];
            $this->writeln("FATAL: Missing Person ID for $text");
            return false;
        }
        return $id;
    }

    /**
     * Check if the provided edition matches any of the provided URLs.
     *
     * @param EditionEntityInterface $edition Edition to check
     * @param array                  $urls    URLs to check
     *
     * @return bool
     */
    protected function checkEditionFullTextMatch(EditionEntityInterface $edition, array $urls): bool
    {
        if (!empty($urls)) {
            $urlNormalizer = function ($url) {
                return str_replace('%3A', ':', $url);
            };
            $normalizedUrls = array_map($urlNormalizer, $urls);
            $fulltext = $this->getDbService(EditionsFullTextService::class);
            foreach ($fulltext->getFullTextForEdition($edition->getId()) as $ft) {
                if (in_array($urlNormalizer($ft->getUrl()), $normalizedUrls)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Given a list of editions, make the user pick one.
     *
     * @param \Iterable $children List of editions to check.
     * @param array     $urls     URLs identifying the desired edition; if a
     * match is found, the selection menu will be automatically bypassed.
     *
     * @return object
     */
    protected function pickEdition($children, $urls = [])
    {
        $options = '';
        $menu = [];
        $choices = [];
        foreach ($children as $i => $current) {
            if ($this->checkEditionFullTextMatch($current, $urls)) {
                return $current;
            }
            $letter = chr(65 + $i);
            $options .= $letter;
            $menu[] = $letter . '. ' . $current['Edition_Name'];
            $choices[] = $current;
        }
        $menuString = implode("\n", $menu);
        $menuHash = md5($menuString);
        if (isset($this->editionPreferences[$menuHash])) {
            $char = $this->editionPreferences[$menuHash];
        } else {
            $this->writeln('Multiple editions found at same position.');
            if (!empty($urls)) {
                $this->writeln(
                    'Incoming URL(s): ' . implode(' | ', array_unique($urls))
                );
            }
            $this->writeln('Please pick one:');
            $this->writeln($menuString);
            $char = strtoupper(
                $this->getCharSelection("\nPlease select one: ", $options)
            );
            $this->editionPreferences[$menuHash] = $char;
        }
        return $choices[ord($char) - 65];
    }

    /**
     * Match up incoming contents against an existing series entry; return an array
     * of arrays, each containing an array of incoming contents data as the first
     * element and a matching array of edition/item data (or false) as the second
     * element.
     *
     * @param SeriesEntityInterface $seriesObj Series row for containing series.
     * @param int                   $pos       Position of incoming contents within series.
     * @param array                 $details   The details from ModsExtractor or equivalent
     * @param ?int                  $item      Optional item ID (used for disambiguation when $pos == 0)
     *
     * @return array|false
     */
    protected function synchronizeSeriesEntries(
        SeriesEntityInterface $seriesObj,
        int $pos,
        array $details,
        ?int $item
    ): array|false {
        $contents = $details['contents'];
        $lookup = $this->getDbService(EditionService::class)->getBySeriesAndPosition($seriesObj, $pos);
        $sorted = [];
        foreach ($lookup as $child) {
            // If we have an item ID filter, apply it now:
            if ($pos === 0 && !empty($item) && $item != $child->getItem()->getId()) {
                continue;
            }
            $sorted[$child->getReplacementNumber()][] = $child;
        }
        // Special case -- no matches found; test with an empty array.
        if (empty($sorted)) {
            return $this->synchronizeSeriesEntriesHelper([], $contents);
        }

        // When replacement numbers are involved, let's try each one separately
        // until we find one that works....
        foreach ($sorted as $replacementNo => $children) {
            if (count($sorted) > 1) {
                $this->writeln("Trying replacement no. $replacementNo");
            }
            if (count($children) > 1) {
                $children = [$this->pickEdition($children, $details['url'] ?? [])];
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
     * @return array|false
     */
    protected function synchronizeSeriesEntriesHelper(array $lookup, array $contents): array|false
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
            // First try exact match:
            foreach ($children as & $currentChild) {
                $titlesChecked[] = $currentChild['item']['Item_Name'] . ' vs. '
                    . $currentContent['title'];
                $match = $this
                    ->checkItemTitles($currentChild['item'], $currentContent, false);
                if ($match) {
                    $result[] = [$currentContent, $currentChild];
                    $currentChild['matched'] = true;
                    break;
                }
            }
            // Next try inexact match:
            if (!$match) {
                foreach ($children as & $currentChild) {
                    $match = $this
                        ->checkItemTitles($currentChild['item'], $currentContent);
                    if ($match) {
                        $result[] = [$currentContent, $currentChild];
                        $currentChild['matched'] = true;
                        break;
                    }
                }
            }
            // No match found:
            if (!$match) {
                $result[] = [$currentContent, false];
            }
        }

        // Fail if we have any existing data not matched up with new data....
        foreach ($children as $child) {
            if (!isset($child['matched'])) {
                foreach ($titlesChecked as $titleChecked) {
                    $this->writeln('Title checked: ' . $titleChecked);
                }
                $this->writeln(
                    'FATAL: No series match found for edition '
                    . $child['edition']['Edition_ID']
                );
                return false;
            }
        }
        return $result;
    }

    /**
     * Match up incoming contents against an existing edition; return an array of
     * arrays, each containing an array of incoming contents data as the first
     * element and a matching array of edition/item data (or false) as the second
     * element.
     *
     * @param object $editionObj Edition row for top-level edition.
     * @param array  $contents   The 'contents' section of the details from
     * ModsExtractor or equivalent
     *
     * @return array
     */
    protected function synchronizeChildren($editionObj, $contents)
    {
        $lookup = $this->getDbService(EditionService::class)->getChildren($editionObj);
        $children = [];
        foreach ($lookup as $child) {
            $children[] = [
                'edition' => $child,
                'item' => $this->getItemForEdition($child),
            ];
        }

        $result = [];
        foreach ($contents as $currentContent) {
            $match = false;
            // First try exact match:
            foreach ($children as & $currentChild) {
                $match = $this->checkItemTitles($currentChild['item'], $currentContent, false);
                if ($match) {
                    $result[] = [$currentContent, $currentChild];
                    $currentChild['matched'] = true;
                    break;
                }
            }
            // Next try inexact match:
            if (!$match) {
                foreach ($children as & $currentChild) {
                    $match = $this->checkItemTitles($currentChild['item'], $currentContent);
                    if ($match) {
                        $result[] = [$currentContent, $currentChild];
                        $currentChild['matched'] = true;
                        break;
                    }
                }
            }
            // No match found:
            if (!$match) {
                $result[] = [$currentContent, false];
            }
        }

        // Fail if we have any existing data not matched up with new data....
        $matches = 0;
        foreach ($children as $child) {
            if (!isset($child['matched'])) {
                $this->writeln("Unmatched item: {$child['item']['Item_Name']}");
                foreach ($contents as $current) {
                    $this->writeln('Possible match: ' . $current['title']);
                }
                $this->writeln(
                    'WARNING: No child match found for edition '
                    . $child['edition']['Edition_ID']
                );
                $result[] = [null, $child];
            } else {
                $matches++;
            }
        }
        if ($matches === 0) {
            $this->writeln('FATAL: No child matches found.');
            return false;
        }
        return $result;
    }

    /**
     * Check the consistency of the incoming series data with existing database
     * entries.
     *
     * @param array                  $details    Details from ModsExtractor or equivalent
     * @param EditionEntityInterface $editionObj Edition row
     * @param array                  $series     Series summary array (see getSeriesForEdition)
     *
     * @return True if all is well.
     */
    protected function validateSeries(array $details, EditionEntityInterface $editionObj, array $series): bool
    {
        if (!isset($details['series'])) {
            $this->writeln('No series found.');
            return false;
        }
        $expectedNumber = intval($editionObj->getPosition());
        foreach ($details['series'] as $seriesName => $number) {
            $actualNumber = intval(preg_replace('/[^0-9]/', '', $number));
            //$this->writeln("Comparing {$expectedNumber} to {$actualNumber}...");
            if (
                $actualNumber == $expectedNumber
                && $this->checkSeriesTitle($series, $seriesName)
            ) {
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
     * @return string
     */
    protected function fuzz(string $str): string
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
    protected function fuzzyCompare(string $str1, string $str2): bool
    {
        //$this->writeln("Comparing {$str1} to {$str2}...");
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
    protected function fuzzyContains(string $haystack, string $needle): bool
    {
        return strstr($this->fuzz($haystack), $this->fuzz($needle));
    }

    /**
     * Do a fuzzy compare to validate an item title.
     *
     * @param array  $item    Item summary array (see getItemForEdition)
     * @param string $title   Title from incoming data, to be checked.
     * @param bool   $inexact Allow inexact matching?
     *
     * @return bool
     */
    protected function checkItemTitle($item, $title, $inexact = true)
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
            if ($inexact && $this->fuzzyContains($longer, $shorter)) {
                $this->writeln(
                    "WARNING: inexact title match {$title} vs. {$itemTitle}"
                );
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
     * @param bool  $inexact        Allow inexact matching?
     *
     * @return bool
     */
    protected function checkItemTitles($item, $currentContent, $inexact = true)
    {
        if ($this->checkItemTitle($item, $currentContent['title'], $inexact)) {
            return true;
        }
        if (isset($currentContent['altTitles'])) {
            foreach ($currentContent['altTitles'] as $title) {
                if ($this->checkItemTitle($item, $title, $inexact)) {
                    return true;
                }
            }
        }
        return $this->hasMatchingAltTitle(
            $currentContent['title'],
            $item['Item_ID'],
            $item['Item_Name']
        );
    }

    /**
     * Do a fuzzy compare to validate a series title.
     *
     * @param array  $series Series summary array (see getSeriesForEdition)
     * @param string $title  Title from incoming data, to be checked.
     *
     * @return bool
     */
    protected function checkSeriesTitle(array $series, string $title): bool
    {
        $seriesTitle = !empty($series['Series_AltName'])
            ? $series['Series_AltName'] : $series['Series_Name'];
        return $this->fuzzyCompare($title, $seriesTitle);
    }

    /**
     * Given an edition row object, return a summary item array.
     *
     * @param EditionEntityInterface $rowObj Edition row
     *
     * @return array
     */
    protected function getItemForEdition(EditionEntityInterface $rowObj): array
    {
        $itemObj = $rowObj->getItem();
        $item = $itemObj->toArray();
        if ($tmpRow = $rowObj->getPreferredItemAlternateTitle()) {
            $item['Item_AltName'] = $tmpRow->getAltName();
        }
        return $item;
    }

    /**
     * Given an edition row object, return a summary series array.
     *
     * @param EditionEntityInterface $rowObj Edition row
     *
     * @return array
     */
    protected function getSeriesForEdition(EditionEntityInterface $rowObj): array
    {
        $seriesObj = $rowObj->getSeries();
        $series = $seriesObj->toArray();
        if ($tmpRow = $rowObj->getPreferredSeriesAlternateTitle()) {
            $series['Series_AltName'] = $tmpRow->getAltName();
        }
        return $series;
    }
}
