<?php

/**
 * Console command: make issue from edition
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

use GeebyDeeby\Db\Table\Edition;
use GeebyDeeby\Db\Table\Series;
use GeebyDeebyLocal\Ingest\IssueMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: make issue from edition
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IssueCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'make/issue';

    /**
     * Issue maker
     *
     * @var IssueMaker
     */
    protected $issueMaker;

    /**
     * Edition table
     *
     * @var Edition
     */
    protected $editionTable;

    /**
     * Series table
     *
     * @var Series
     */
    protected $seriesTable;

    /**
     * Constructor
     *
     * @param IssueMaker  $issueMaker Issue maker
     * @param Edition     $edition    Edition table
     * @param Series      $series     Series table
     * @param string|null $name       The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(
        IssueMaker $issueMaker,
        Edition $edition,
        Series $series,
        $name = null
    ) {
        $this->issueMaker = $issueMaker;
        $this->editionTable = $edition;
        $this->seriesTable = $series;
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
            ->setDescription('Format a work edition into an issue')
            ->setHelp('Creates issue container around a work edition.')
            ->addArgument(
                'edition',
                InputArgument::REQUIRED,
                'Edition ID to convert'
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
        $edition = $input->getArgument('edition');
        $prefix = $input->getArgument('prefix');
        $editionObj = $this->editionTable->getByPrimaryKey($edition);
        if (!$editionObj) {
            $output->writeln("Cannot find edition match for $edition");
            return 1;
        }
        $seriesObj = $this->seriesTable->getByPrimaryKey($editionObj->Series_ID);
        $this->issueMaker->setOutputInterface($output);
        $this->issueMaker->createIssueForWork(
            $editionObj,
            empty($prefix) ? $seriesObj->Series_Name . ' #' : $prefix
        );
        return 0;
    }
}
