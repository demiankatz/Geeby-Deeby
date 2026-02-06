<?php

/**
 * Console command: make issues
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

namespace GeebyDeebyLocal\Command\Make;

use GeebyDeeby\Db\Service\SeriesService;
use GeebyDeebyLocal\Ingest\ConsoleIssueMaker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: make issues
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
#[AsCommand(
    name: 'make/issues',
    description: 'Format series entries into issues'
)]
class IssuesCommand extends Command
{
    /**
     * Constructor
     *
     * @param ConsoleIssueMaker $issueMaker    Issue maker
     * @param SeriesService     $seriesService Series table
     * @param string|null       $name          The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(
        protected ConsoleIssueMaker $issueMaker,
        protected SeriesService $seriesService,
        $name = null
    ) {
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
            ->setHelp('Creates issue containers around works in a series.')
            ->addArgument(
                'series',
                InputArgument::REQUIRED,
                'Series ID to convert'
            )->addArgument(
                'prefix',
                InputArgument::OPTIONAL,
                'Text to prefix to number for issue names (omit to use series name)',
                ''
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
        $series = $input->getArgument('series');
        $prefix = $input->getArgument('prefix');
        $seriesObj = $this->seriesService->getByPrimaryKey($series);
        if (!$seriesObj) {
            $output->writeln("Cannot find series match for $series");
            return 1;
        }
        $this->issueMaker->setOutputInterface($output);
        $this->issueMaker->makeIssues(
            $seriesObj,
            empty($prefix) ? $seriesObj->getSeriesName() . ' #' : $prefix
        );
        return 0;
    }
}
