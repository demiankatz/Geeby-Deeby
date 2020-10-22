<?php
/**
 * Console command: ingest spreadsheet
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
use GeebyDeeby\Db\Table\SeriesAltTitles;
use GeebyDeebyLocal\Ingest\DatabaseIngester;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command: ingest spreadsheet
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SpreadsheetCommand extends Command
{
    use \GeebyDeebyLocal\Command\SeriesByTitleTrait;

    /**
     * The name of the command (the part after "cli.php")
     *
     * @var string
     */
    protected static $defaultName = 'ingest/spreadsheet';

    /**
     * Editions table
     *
     * @var Edition
     */
    protected $editions;

    /**
     * Database ingester
     *
     * @var DatabaseIngester
     */
    protected $ingester;

    /**
     * Constructor
     *
     * @param Series           $series     Series table
     * @param SeriesAltTitles  $seriesAlts SeriesAltTitles table
     * @param Edition          $editions   Edition table
     * @param DatabaseIngester $ingester   Database ingester
     * @param string|null      $name       The name of the command; passing null
     * means it must be set in configure()
     */
    public function __construct(Series $series, SeriesAltTitles $seriesAlts,
        Edition $editions, DatabaseIngester $ingester, $name = null
    ) {
        $this->series = $series;
        $this->seriesAltTitles = $seriesAlts;
        $this->editions = $editions;
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
            ->setDescription('Ingest metadata from CSV spreadsheet')
            ->setHelp('Loads data from a CSV spreadsheet of metadata.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Spreadsheet (CSV) containing metadata'
            );
    }

    /**
     * Map a line from a spreadsheet into a details array.
     *
     * @param array $line Raw input line
     *
     * @return array
     */
    protected function spreadsheetLineToDetails($line)
    {
        list($title, $author, $date, $place, $publisher, $series, $number, $url)
            = $line;
        $content = compact('title');
        if (!empty($author)) {
            $content['authors'] = [['name' => $author]];
        }
        $details = [
            'contents' => [$content],
            'series' => [$series => $number],
        ];
        if (!empty($url)) {
            $details['url'] = [$url];
        }
        if (!empty($publisher)) {
            $details['publisher'] = ['name' => $publisher, 'place' => $place];
        }
        if (!empty($date)) {
            $details['date'] = $date;
        }
        return $details;
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
        $file = $input->getArgument('file');
        if (!file_exists($file)) {
            $output->writeln("Invalid/missing spreadsheet: $file");
            return 1;
        }
        $this->ingester->setOutputInterface($output);
        $total = $success = 0;
        $handle = fopen($file, 'r');
        $continuePrompt = 'Continue with next item anyway?';
        while ($line = fgetcsv($handle)) {
            if (count($line) < 7) {
                $output->writeln("Short line encountered; breaking out...");
                break;
            }
            if (!isset($line[7])) {
                $line[7] = null;
            }
            $total++;
            $details = $this->spreadsheetLineToDetails($line);
            $series = array_keys($details['series'])[0];
            $seriesObj = $this->getSeriesByTitle($series, $output);
            if (!$seriesObj) {
                $output->writeln("Cannot find series match for $series");
                if ($this->ingester->askQuestion($continuePrompt)) {
                    continue;
                }
                break;
            } elseif (!$this->ingester->ingest($details, 'series', $seriesObj)) {
                if ($this->ingester->askQuestion($continuePrompt)) {
                    continue;
                }
                break;
            }
            $output->writeln('---');
            $success++;
        }
        fclose($handle);
        $output->writeln("Successfully processed $success of $total rows.");
        return 0;
    }
}
