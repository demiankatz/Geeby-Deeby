<?php

/**
 * About controller
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2012.
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Controller;

use GeebyDeeby\Db\Service\SeriesService;

/**
 * About controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AboutController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Credits page
     *
     * @return mixed
     */
    public function creditsAction()
    {
        return $this->createViewModel();
    }

    /**
     * About page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->createViewModel();
    }

    /**
     * Progress page
     *
     * @return mixed
     */
    public function progressAction()
    {
        $stats = $this->getDbService(SeriesService::class)->getProgressStatistics();
        return $this->createViewModel(['progress' => $stats]);
    }
}
