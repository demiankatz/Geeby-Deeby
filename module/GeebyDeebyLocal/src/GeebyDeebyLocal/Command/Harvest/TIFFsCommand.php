<?php

/**
 * Console command: harvest TIFFs from NIU
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

use GeebyDeebyLocal\Ingest\SolrHarvester;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: harvest TIFFs from NIU
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
#[AsCommand(
    name: 'harvest/tiffs',
    description: 'Harvest TIFFs from NIU'
)]
class TIFFsCommand extends Command
{
    /**
     * Base URL for Islandora front-end.
     *
     * @var string
     */
    protected $baseUrl = 'https://dimenovels.lib.niu.edu/islandora/object/';

    /**
     * Solr harvester
     *
     * @var SolrHarvester
     */
    protected $solr;

    /**
     * Constructor
     *
     * @param SolrHarvester $solr Solr harvester
     * @param string|null   $name The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(SolrHarvester $solr, $name = null)
    {
        $this->solr = $solr;
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
            ->setHelp('Harvests TIFFs for an object in NIU\'s repository.')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Target directory for harvested images'
            )->addArgument(
                'pid',
                InputArgument::REQUIRED,
                'PID of resource to harvest'
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
        $this->solr->setOutputInterface($output);
        $results = $this->solr->getAllPagePIDs($input->getArgument('pid'));
        foreach ($results as $page => $pid) {
            $output->writeln("Downloading page $page...");
            file_put_contents(
                $dir . '/' . str_pad($page, 5, '0', STR_PAD_LEFT) . '.tiff',
                file_get_contents(
                    $this->baseUrl . urlencode($pid) . '/datastream/OBJ/download'
                )
            );
        }
        return 0;
    }
}
