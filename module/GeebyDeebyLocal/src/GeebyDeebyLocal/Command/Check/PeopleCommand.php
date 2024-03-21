<?php

/**
 * Console command: check people headings
 *
 * PHP version 7
 *
 * Copyright (C) Demian Katz 2021.
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

namespace GeebyDeebyLocal\Command\Check;

use GeebyDeeby\Db\Table\PeopleURIs;
use GeebyDeeby\View\Helper\ShowPerson;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: check people headings
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'check/people';

    /**
     * Database table object for fetching information.
     *
     * @var PeopleURIs
     */
    protected $table;

    /**
     * Constructor
     *
     * @param PeopleURIs  $table The PeopleURIs database table object
     * @param string|null $name  The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(PeopleURIs $table, $name = null)
    {
        $this->table = $table;
        parent::__construct($name);
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Check people headings against linked data URIs')
            ->setHelp('Confirms that people headings are valid and links are good.')
            ->addArgument(
                'startFrom',
                InputArgument::OPTIONAL,
                'Initial last name (or letter) to begin checking from',
                ''
            );
    }

    /**
     * Given an RDF URI, fetch the expected heading.
     *
     * @param string $uri URI to check
     *
     * @return string
     */
    protected function getExpectedHeading($uri)
    {
        $rdf = \EasyRdf\Graph::newAndLoad($uri, 'rdfxml');
        return (string)$rdf->resource()->get('skos:prefLabel');
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
        $startFrom = $input->getArgument('startFrom');
        $callback = empty($startFrom) ? false
            : function ($select) use ($startFrom) {
                $select->where->greaterThan('Last_Name', $startFrom);
            };
        $list = $this->table->getPeopleWithURIs($callback);
        $nameFormatter = new ShowPerson();
        if (!$list) {
            $output->writeln('Cannot find data.');
            return 1;
        }
        $output->writeln(
            implode("\t", ['local name', 'remote name', 'match?', 'uri'])
        );
        foreach ($list as $current) {
            $actualHeading = $nameFormatter($current);
            $uri = $current->URI;
            try {
                $expectedHeading = $this->getExpectedHeading($uri);
            } catch (\Exception $e) {
                $expectedHeading = 'ERROR: ' . $e->getMessage();
            }
            $match = $actualHeading === $expectedHeading ? 'yes' : 'no';
            $output->writeln(
                implode("\t", [$actualHeading, $expectedHeading, $match, $uri])
            );
        }
        return 0;
    }
}
