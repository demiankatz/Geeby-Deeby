<?php
/**
 * Console command: ingest IIIF
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
namespace GeebyDeebyLocal\Command\Ingest;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: ingest IIIF
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IIIFCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'ingest/iiif';

    /**
     * Ingesters
     *
     * @var array
     */
    protected $ingesters;

    /**
     * Constructor
     *
     * @param array       $ingesters Ingesters
     * @param string|null $name      The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(array $ingesters, $name = null)
    {
        $this->ingesters = $ingesters;
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
            ->setDescription('Ingest IIIF content')
            ->setHelp('Loads IIIF images from available sources.');
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
        foreach ($this->ingesters as $label => $ingester) {
            $output->writeln("Checking images for " . $label);
            $ingester->setOutputInterface($output);
            $ingester->ingestImages();
        }
        return 0;
    }
}
