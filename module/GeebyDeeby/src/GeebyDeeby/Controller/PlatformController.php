<?php

/**
 * Platform controller
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

use GeebyDeeby\Db\Service\EditionsPlatformService;
use GeebyDeeby\Db\Service\PlatformService;

use function is_object;

/**
 * Platform controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PlatformController extends AbstractBase
{
    /**
     * "Show platform" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        $entity = (null === $id) ? null : $this->getDbService(PlatformService::class)->getByPrimaryKey(intval($id));
        if (!is_object($entity)) {
            return $this->forwardTo(__NAMESPACE__ . '\Platform', 'notfound');
        }
        $view = $this->createViewModel(
            ['platform' => $entity->toArray()]
        );
        $view->items = $this->getDbService(EditionsPlatformService::class)->getItemsForPlatform($id);
        return $view;
    }

    /**
     * Platform list
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->createViewModel(
            ['platforms' => $this->getDbService(PlatformService::class)->getList()]
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
