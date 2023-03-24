<?php

/**
 * Console command: harvest records for a collection
 *
 * PHP version 7
 *
 * Copyright (C) Demian Katz 2020.
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
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Command\Harvest;

use GeebyDeeby\Db\Table\Series;
use GeebyDeeby\Db\Table\SeriesAltTitles;
use GeebyDeebyLocal\Ingest\FedoraHarvester;
use GeebyDeebyLocal\Ingest\SolrHarvester;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: harvest records for a collection
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class CollectionCommand extends Command
{
    use \GeebyDeebyLocal\Command\SeriesByTitleTrait;

    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'harvest/collection';

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
     *
     * @param FedoraHarvester $fedora     Fedora harvester
     * @param SolrHarvester   $solr       Solr harvester
     * @param Series          $series     Series table object
     * @param SeriesAltTitles $seriesAlts Series_AltTitles table object
     * @param string|null     $name       The name of the command; passing null means
     * it must be set in configure()
     */
    public function __construct(
        FedoraHarvester $fedora,
        SolrHarvester $solr,
        Series $series,
        SeriesAltTitles $seriesAlts,
        $name = null
    ) {
        $this->fedora = $fedora;
        $this->solr = $solr;
        $this->series = $series;
        $this->seriesAltTitles = $seriesAlts;
        parent::__construct($name);
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Harvest a series from NIU')
            ->setHelp(
                'Harvests records from a series to a directory for later processing.'
            )->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Target directory for harvested metadata'
            )->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'Collection PID to harvest'
            )->addArgument(
                'series',
                InputArgument::REQUIRED,
                'Series name to add collection items into'
            );
    }

    /**
     * Run the command.
     *
     * @param InputInterface  $input  Input object
     * @param OutputInterface $output Output object
     *
     * @return int 0 for success
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = rtrim($input->getArgument('dir'), '/');
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                $output->writeln("Cannot create directory '$dir'");
                return 1;
            }
        }
        $this->fedora->setOutputInterface($output);
        $this->solr->setOutputInterface($output);
        $collection = $input->getArgument('collection');
        $series = $input->getArgument('series');
        $seriesObj = $this->getSeriesByTitle($series, $output);
        if (!$seriesObj) {
            $output->writeln("Cannot find series match for $series");
            return 1;
        }
        $entries = $this->solr->getCollectionEntries($collection);
        $count = 0;
        foreach ($entries as $pid) {
            $rawMods = $this->fedora->getModsForPid($pid);
            if (!$rawMods) {
                $output->writeln("Could not retrieve MODS for $pid.");
                return 1;
            }
            file_put_contents($dir . '/' . $count . '.mods', $rawMods);
            $count++;
        }
        file_put_contents(
            $dir . '/job.json',
            json_encode(
                [
                    'type' => 'series',
                    'id' => $seriesObj->Series_ID,
                    'count' => $count]
            )
        );
        return 0;
    }
}
