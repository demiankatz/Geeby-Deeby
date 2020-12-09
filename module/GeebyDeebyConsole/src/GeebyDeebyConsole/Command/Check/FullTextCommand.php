<?php
/**
 * Console command: check full text links
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
namespace GeebyDeebyConsole\Command\Check;

use GeebyDeeby\Db\Table\EditionsFullText;
use Laminas\Http\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: commit to Solr
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class FullTextCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'check/fulltext';

    /**
     * Database table for retrieving full text from editions.
     *
     * @var EditionsFullText
     */
    protected $table;

    /**
     * Constructor
     *
     * @param EditionsFullText $table  Database table for retrieving full text from
     * editions
     * @param Client           $client HTTP client
     * @param string|null      $name   The name of the command; passing null means it
     * must be set in configure()
     */
    public function __construct(EditionsFullText $table, Client $client = null,
        $name = null
    ) {
        $this->table = $table;
        $this->client = $client ?? new Client();
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
            ->setDescription('Full text link checker')
            ->setHelp(
                'Checks for problems with full text links; '
                . 'outputs a CSV with URLs and status codes.'
            )->addArgument(
                'series',
                InputArgument::OPTIONAL,
                'Series ID to check (* for all)',
                '*'
            )->addArgument(
                'provider',
                InputArgument::OPTIONAL,
                'Full text provider ID to check (* for all)',
                '*'
            )->addOption(
                'updateRedirects',
                null,
                InputOption::VALUE_NONE,
                'If a redirect is encountered, update the database to match; '
                . 'also adds a new column to CSV output set to 1 for updated URLs.'
            );
    }

    /**
     * Update a full text URL.
     *
     * @param int    $id  Sequence ID of full text URL
     * @param string $url New URL
     *
     * @return void
     */
    protected function updateUrl(int $id, string $url): void
    {
        $this->table->update(
            ['Full_Text_URL' => $url],
            ['Sequence_ID' => $id]
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
        if ($series == '*') {
            $series = null;
        }
        $provider = $input->getArgument('provider');
        if ($provider == '*') {
            $provider = null;
        }
        $updateRedirects = $input->getOption('updateRedirects');
        $items = $this->table->getItemsWithFullText($series, false, $provider);
        foreach ($items as $current) {
            $url = $current->Full_Text_URL;
            $request = new \Laminas\Http\Request();
            $response = $this->client->send($request->setUri($url));
            $rewritten = 0;
            if ($updateRedirects && $this->client->getRedirectionsCount() > 0) {
                $rewritten = 1;
                $url = $this->client->getUri();
                $this->updateUrl($current->Sequence_ID, $url);
            }
            $responseLine = [$url, $response->getStatusCode()];
            if ($updateRedirects) {
                $responseLine[] = $rewritten;
            }
            $output->writeln(implode(',', $responseLine));
        }
        return 0;
    }
}
