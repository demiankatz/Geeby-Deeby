<?php

/**
 * Adds getSeriesByTitle method to Command classes; assumes the presence of
 * $this->series and $this->seriesAltTitles database table classes.
 *
 * PHP version 8
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

namespace GeebyDeebyLocal\Command;

use GeebyDeeby\Db\Entity\SeriesEntityInterface;
use GeebyDeeby\Db\Service\SeriesAltTitleService;
use GeebyDeeby\Db\Service\SeriesService;
use Symfony\Component\Console\Output\OutputInterface;

use function count;

/**
 * Adds getSeriesByTitle method to Command classes; assumes the presence of
 * $this->series and $this->seriesAltTitles database table classes.
 *
 * @category GeebyDeeby
 * @package  Console
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
trait SeriesByTitleTrait
{
    /**
     * Series table
     *
     * @var SeriesService
     */
    protected SeriesService $series;

    /**
     * Series_AltTitles table
     *
     * @var SeriesAltTitleService
     */
    protected SeriesAltTitleService $seriesAltTitles;

    /**
     * Retrieve a series object from the database for a given title.
     *
     * @param string          $title  Title
     * @param OutputInterface $output Output interface
     *
     * @return ?SeriesEntityInterface
     */
    protected function getSeriesByTitle($title, OutputInterface $output): ?SeriesEntityInterface
    {
        $result = $this->series->getSeriesByName($title);
        if (count($result) != 1) {
            if (count($result) === 0) {
                $output->writeln('No primary title match; trying alternate titles.');
                $altResult = $this->seriesAltTitles->getByAltTitle($title);
                if (count($altResult) === 1) {
                    $result = [$altResult[0]->getSeries()];
                }
            }
            if (count($result) != 1) {
                $output->writeln('Unexpected result count: ' . count($result));
                return null;
            }
        }
        return $result[0] ?? null;
    }
}
