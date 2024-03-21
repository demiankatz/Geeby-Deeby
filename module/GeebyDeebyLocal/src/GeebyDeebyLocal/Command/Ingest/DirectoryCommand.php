<?php

/**
 * Console command: ingest directory
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

use GeebyDeeby\Db\Table\Edition;
use GeebyDeeby\Db\Table\Series;
use GeebyDeebyLocal\Ingest\DatabaseIngester;
use GeebyDeebyLocal\Ingest\ModsExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function is_object;

/**
 * Console command: ingest directory
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class DirectoryCommand extends Command
{
    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'ingest/directory';

    /**
     * Series table
     *
     * @var Series
     */
    protected $series;

    /**
     * Editions table
     *
     * @var Edition
     */
    protected $editions;

    /**
     * MODS extractor
     *
     * @var ModsExtractor
     */
    protected $extractor;

    /**
     * Database ingester
     *
     * @var DatabaseIngester
     */
    protected $ingester;

    /**
     * Constructor
     *
     * @param Series           $series    Series table
     * @param Edition          $editions  Edition table
     * @param ModsExtractor    $extractor MODS extractor
     * @param DatabaseIngester $ingester  Database ingester
     * @param string|null      $name      The name of the command; passing null means
     * it must be set in configure()
     */
    public function __construct(
        Series $series,
        Edition $editions,
        ModsExtractor $extractor,
        DatabaseIngester $ingester,
        $name = null
    ) {
        $this->series = $series;
        $this->editions = $editions;
        $this->extractor = $extractor;
        $this->ingester = $ingester;
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
            ->setDescription('Ingest directory of harvested MODS')
            ->setHelp('Loads data from a directory of harvested MODS files.')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Source directory containing harvested metadata'
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
        $job = json_decode(file_get_contents($dir . '/job.json'));
        if (!is_object($job)) {
            $output->writeln("Invalid/missing job.json in $dir");
            return 1;
        }
        switch ($job->type) {
            case 'series':
                // for series, extra is series object, loaded once...
                $extra = $this->series->getByPrimaryKey($job->id);
                break;
            case 'existing':
                break;
            default:
                $output->writeln("Invalid/missing job.json in $dir");
                return;
        }

        $this->ingester->setOutputInterface($output);
        $success = 0;
        for ($i = 0; $i < $job->count; $i++) {
            $modsFile = $dir . '/' . $i . '.mods';
            $output->writeln("Opening $modsFile...");
            $details = $this->extractor->getDetails(
                simplexml_load_string(file_get_contents($modsFile))
            );
            // for existing, extra is edition object, loaded for each record...
            if ($job->type == 'existing') {
                $extras = json_decode(file_get_contents($dir . '/' . $i . '.json'));
                if (!isset($extras->edition)) {
                    $output->writeln("Missing edition data in $i.json");
                    return 1;
                }
                $extra = $this->edition->getByPrimaryKey($extras->edition);
            }
            if (!$this->ingester->ingest($details, $job->type, $extra)) {
                $prompt = 'Continue with next item anyway?';
                if ($this->ingester->askQuestion($prompt)) {
                    continue;
                }
                break;
            }
            $output->writeln('---');
            $success++;
        }
        $output
            ->writeln("Successfully processed $success of {$job->count} editions.");
        return 0;
    }
}
