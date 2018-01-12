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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;
use GeebyDeebyLocal\Ingest\DatabaseIngester;
use GeebyDeebyLocal\Ingest\IssueMaker;
use GeebyDeebyLocal\Ingest\ModsExtractor;
use Zend\Console\Console, Zend\Console\Prompt;

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
     * Fedora harvester
     *
     * @var FedoraHarvester
     */
    protected $fedora;

    /**
     * Solr harvester
     *
     * @var SolrHarvester
     */
    protected $solr;

    /**
     * Constructor
     */
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
            file_put_contents($dir . '/' . $count . '.json', json_encode(['edition' => $edition]));
            $count++;
        }
        file_put_contents($dir . '/job.json', json_encode(['type' => 'existing', 'count' => $count]));
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
        file_put_contents($dir . '/job.json', json_encode(['type' => 'series', 'id' => $seriesObj->Series_ID, 'count' => $count]));
        Console::writeLine("Successfully harvested $count records.");
    }

    /**
     * Ingest the contents of a harvest directory.
     *
     * @return mixed
     */
    public function directoryAction()
    {
        $dir = rtrim($this->params()->fromRoute('dir'), '/');
        $job = json_decode(file_get_contents($dir . '/job.json'));
        if (!is_object($job)) {
           Console::writeLine("Invalid/missing job.json in $dir");
           return;
        }
        switch ($job->type) {
            case 'series':
                // for series, extra is series object, loaded once...
                $extra = $this->getDbTable('series')->getByPrimaryKey($job->id);
                break;
            case 'existing':
                break;
            default:
                Console::writeLine("Invalid/missing job.json in $dir");
                return;
        }

        $extractor = new ModsExtractor();
        $ingester = $this->getIngester();
        $success = 0;
        for ($i = 0; $i < $job->count; $i++) {
            $details = $extractor->getDetails(
                simplexml_load_string(
                    file_get_contents($dir . '/' . $i . '.mods')
                )
            );
            // for existing, extra is edition object, loaded for each record...
            if ($job->type == 'existing') {
                $extras = json_decode(file_get_contents($dir . '/' . $i . '.json'));
                if (!isset($extras->edition)) {
                    Console::writeLine("Missing edition data in $i.json");
                    return;
                }
                $extra = $this->getDbTable('edition')->getByPrimaryKey($extras->edition);
            }
            if (!$ingester->ingest($details, $job->type, $extra)) {
                if (Prompt\Confirm::prompt('Continue with next item anyway? (y/n) ')) {
                    continue;
                }
                break;
            }
            $success++;
        }
        Console::writeLine("Successfully processed $success of {$job->count} editions.");
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
     * Create Issue containers around Works in a series.
     *
     * @return mixed
     */
    public function makeissuesAction()
    {
        $series = $this->params()->fromRoute('series');
        $seriesObj = $this->getSeriesById($series);
        if (!$seriesObj) {
            Console::writeLine("Cannot find series match for $series");
            return;
        }
        $prefix = $this->params()->fromRoute('prefix');
        $prefix = empty($prefix) ? $seriesObj->Series_Name . ' #' : $prefix;
        $this->getIssueMaker()->makeIssues($seriesObj, $prefix);
    }

    /**
     * Ingest series entries (by searching series title) action.
     *
     * @return mixed
     */
    public function seriesAction()
    {
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

    /**
     * Given a Fedora PID, load data into the database from the external site.
     *
     * @param string $pid       Fedora PID
     * @param object $seriesObj Series object
     *
     * @return bool True for success.
     */
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
        return $this->getIngester()->ingest($details, 'series', $seriesObj);
    }

    /**
     * Retrieve a series object from the database for a given ID.
     *
     * @param string $id ID
     *
     * @return \GeebyDeeby\Db\Row\Series|bool
     */
    protected function getSeriesById($id)
    {
        return $this->getDbTable('series')->getByPrimaryKey($id);
    }

    /**
     * Retrieve a series object from the database for a given title.
     *
     * @param string $title Title
     *
     * @return \GeebyDeeby\Db\Row\Series|bool
     */
    protected function getSeriesByTitle($title)
    {
        $table = $this->getDbTable('series');
        $result = $table->select(['Series_Name' => $title]);
        if (count($result) != 1) {
            if (count($result) === 0) {
                Console::writeLine('No primary title match; trying alternate titles.');
                $altTable = $this->getDbTable('seriesalttitles');
                $altResult = $altTable->select(['Series_AltName' => $title])->toArray();
                if (count($altResult) === 1) {
                    $result = $table->select(['Series_ID' => $altResult[0]['Series_ID']]);
                }
            }
            if (count($result) != 1) {
                Console::writeLine('Unexpected result count: ' . count($result));
                return false;
            }
        }
        foreach ($result as $current) {
            return $current;
        }
    }

    /**
     * Given an edition ID, load data into the database from the external site.
     *
     * @param string $edition Edition ID.
     *
     * @return bool True for success.
     */
    protected function loadExistingEdition($edition)
    {
        Console::writeLine("Loading existing edition (id = {$edition})");
        $rawMods = $this->fedora->getModsForEdition('https://dimenovels.org/Edition/' . $edition);
        if (!$rawMods) {
            Console::writeLine('Could not retrieve MODS.');
            return false;
        }
        $mods = simplexml_load_string($rawMods);

        $extractor = new ModsExtractor();
        $details = $extractor->getDetails($mods);
        $editionObj = $this->getDbTable('edition')->getByPrimaryKey($edition);

        return $this->getIngester()->ingest($details, 'existing', $editionObj);
    }

    /**
     * Construct the ingestion tool.
     *
     * @return DatabaseIngester
     */
    protected function getIngester()
    {
        $tables = $this->getServiceLocator()->get('GeebyDeeby\DbTablePluginManager');
        return new DatabaseIngester($tables, $this->getServiceLocator()->get('GeebyDeeby\Articles'));
    }

    /**
     * Construct the issue maker tool.
     *
     * @return IssueMaker
     */
    protected function getIssueMaker()
    {
        $tables = $this->getServiceLocator()->get('GeebyDeeby\DbTablePluginManager');
        return new IssueMaker($tables, $this->getServiceLocator()->get('GeebyDeeby\Articles'));
    }
}
