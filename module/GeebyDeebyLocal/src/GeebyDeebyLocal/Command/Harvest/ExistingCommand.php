<?php
/**
 * Console command: harvest records for existing editions
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

use GeebyDeebyLocal\Ingest\FedoraHarvester;
use GeebyDeebyLocal\Ingest\SolrHarvester;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: harvest records for existing editions
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ExistingCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'harvest/existing';

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
     * @param FedoraHarvester $fedora Fedora harvester
     * @param SolrHarvester   $solr   Solr harvester
     * @param string|null     $name   The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(FedoraHarvester $fedora, SolrHarvester $solr,
        $name = null
    ) {
        $this->fedora = $fedora;
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
            ->setDescription('Harvest existing editions from NIU')
            ->setHelp('Harvests NIU metadata for existing editions.')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Target directory for harvested metadata'
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
        $count = 0;
        $this->fedora->setOutputInterface($output);
        $this->solr->setOutputInterface($output);
        foreach ($this->solr->getExistingEditions() as $edition) {
            $rawMods = $this->fedora->getModsForEdition('https://dimenovels.org/Edition/' . $edition);
            if (!$rawMods) {
                $output->writeln("Could not retrieve MODS for $edition.");
                return 1;
            }
            file_put_contents($dir . '/' . $count . '.mods', $rawMods);
            file_put_contents($dir . '/' . $count . '.json', json_encode(['edition' => $edition]));
            $count++;
        }
        file_put_contents($dir . '/job.json', json_encode(['type' => 'existing', 'count' => $count]));
        $output->writeln("Successfully harvested $count records.");
        return 0;
    }
}
