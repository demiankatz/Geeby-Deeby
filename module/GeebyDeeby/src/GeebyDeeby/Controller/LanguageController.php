<?php

/**
 * Language controller
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

namespace GeebyDeeby\Controller;

use GeebyDeeby\Db\Service\LanguageService;
use GeebyDeeby\Db\Service\SeriesService;

use function is_object;

/**
 * Language controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class LanguageController extends AbstractBase
{
    /**
     * "Show language" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        $entity = (null === $id) ? null : $this->getDbService(LanguageService::class)->getByPrimaryKey((int)$id);
        if (!is_object($entity)) {
            return $this->forwardTo(__NAMESPACE__ . '\Language', 'notfound');
        }
        return $this->createViewModel(
            [
                'language' => $entity->toArray(),
                'series' => $this->getDbService(SeriesService::class)->getSeriesForLanguage($id),
            ]
        );
    }

    /**
     * Language list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            ['languages' => $this->getDbService(LanguageService::class)->getList()]
        );
    }

    /**
     * Not found page
     *
     * @return mixed
     */
    public function notfoundAction()
    {
        return $this->createViewModel();
    }
}
