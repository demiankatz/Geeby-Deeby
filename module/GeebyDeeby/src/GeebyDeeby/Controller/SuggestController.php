<?php

/**
 * Autosuggestion controller
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

use GeebyDeeby\Db\Service\EditionService;
use GeebyDeeby\Db\Service\ItemService;
use GeebyDeeby\Db\Service\NoteService;
use GeebyDeeby\Db\Service\PersonService;
use GeebyDeeby\Db\Service\PredicateService;
use GeebyDeeby\Db\Service\PublisherService;
use GeebyDeeby\Db\Service\SeriesService;
use GeebyDeeby\Db\Service\TagService;

use function is_callable;

/**
 * Autosuggestion controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class SuggestController extends AbstractBase
{
    /**
     * Default home page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $serviceMap = [
            'edition' => EditionService::class,
            'item' => ItemService::class,
            'note' => NoteService::class,
            'person' => PersonService::class,
            'predicate' => PredicateService::class,
            'publisher' => PublisherService::class,
            'series' => SeriesService::class,
            'tag' => TagService::class,
        ];
        $serviceName = strtolower($this->params()->fromRoute('table'));
        $service = $this->getDbService($serviceMap[$serviceName] ?? 'undefined');
        if (!$service || !is_callable([$service, 'getSuggestions'])) {
            throw new \Exception('Suggestions not supported.');
        }
        $suggestions = $service->getSuggestions(
            $this->params()->fromQuery('q'),
            $this->params()->fromQuery('limit')
        );
        $headers = $this->getResponse()->getHeaders();
        $headers->addHeaderLine(
            'Content-type',
            'text/plain'
        );
        $response = '';
        foreach ($suggestions as $current) {
            $response .= $current->getPrimaryKeyValue() . ': '
                . $current->getDisplayName() . "\n";
        }
        return $this->getResponse()->setContent($response);
    }
}
