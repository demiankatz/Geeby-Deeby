<?php

/**
 * Edit publisher controller
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

use GeebyDeeby\Db\Service\CityService;
use GeebyDeeby\Db\Service\CountryService;
use GeebyDeeby\Db\Service\PredicateService;
use GeebyDeeby\Db\Service\PublishersAddressService;
use GeebyDeeby\Db\Service\PublisherService;
use GeebyDeeby\Db\Service\PublishersImprintService;
use GeebyDeeby\Db\Service\PublishersUriService;
use GeebyDeeby\Db\Service\SeriesPublisherService;

use function count;

/**
 * Edit publisher controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditPublisherController extends AbstractBase
{
    /**
     * Display a list of platforms
     *
     * @return mixed
     */
    public function listAction()
    {
        return $this->getGenericList(
            PublisherService::class,
            'publishers',
            'geeby-deeby/edit-publisher/render-publishers'
        );
    }

    /**
     * Operate on a single platform
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = ['publisher' => 'setPublisherName'];
        [$view, $ok] = $this->handleGenericItem(PublisherService::class, $assignMap, 'publisher');
        // Add extra fields/controls if outside of a lightbox:
        if ($ok && !$this->getRequest()->isXmlHttpRequest()) {
            $publisherId = $view->affectedEntity->getId();
            $view->cities = $this->getDbService(CityService::class)->getList();
            $view->countries = $this->getDbService(CountryService::class)->getList();
            $view->addresses = $this->getDbService(PublishersAddressService::class)
                ->getAddressesForPublisher($publisherId);
            $view->imprints = $this->getDbService(PublishersImprintService::class)
                ->getImprintsForPublisher($publisherId);
            $view->predicates = $this->getDbService(PredicateService::class)->getList();
            $view->uris = $this->getDbService(PublishersUriService::class)->getURIsForPublisher($publisherId);
            $view->setTemplate('geeby-deeby/edit-publisher/edit-full');
        }
        return $view;
    }

    /**
     * Work with addresses
     *
     * @return mixed
     */
    public function addressAction()
    {
        // Special case: new address:
        if ($this->getRequest()->isPost()) {
            $countryId = $this->params()->fromPost('country');
            if (!$countryId) {
                return $this->jsonDie('Country must be specified.');
            }
            $cityId = $this->params()->fromPost('city');
            $addressService = $this->getDbService(PublishersAddressService::class);
            $entity = $addressService->createEntity()
                ->setPublisher($this->params()->fromRoute('id'))
                ->setCountry((int)$countryId)
                ->setCity($cityId ? (int)$cityId : null)
                ->setStreet($this->params()->fromPost('street'));
            $addressService->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        // Prevent deletion of addresses that are linked up:
        if ($this->getRequest()->isDelete()) {
            $extra = $this->params()->fromRoute('extra');
            $result = $this->getDbService(SeriesPublisherService::class)->getSeriesForAddress($extra);
            if (count($result) > 0) {
                $msg = 'You cannot delete this address; it is used by Series ' . $result[0]['Series_ID'] . '.';
                return $this->jsonDie($msg);
            }
        }
        // Otherwise, treat this as a generic link:
        return $this->handleGenericLink(
            PublishersAddressService::class,
            null,
            null,
            'addresses',
            'getAddressesForPublisher',
            'geeby-deeby/edit-publisher/address-list.phtml',
            retrieveLinkMethod: 'getAddressesForPublisherPublisherAddress'
        );
    }

    /**
     * Work with imprints
     *
     * @return mixed
     */
    public function imprintAction()
    {
        // Special case: new imprint:
        if ($this->getRequest()->isPost()) {
            $name = trim((string)$this->params()->fromPost('imprint'));
            if (empty($name)) {
                return $this->jsonDie('Name must not be empty.');
            }
            $service = $this->getDbService(PublishersImprintService::class);
            $entity = $service->createEntity()
                ->setPublisher($this->params()->fromRoute('id'))
                ->setImprintName($name);
            $service->persistEntity($entity);
            return $this->jsonReportSuccess();
        }
        // Prevent deletion of imprints that are linked up:
        if ($this->getRequest()->isDelete()) {
            $extra = $this->params()->fromRoute('extra');
            $result = $this->getDbService(SeriesPublisherService::class)->getSeriesForImprint($extra);
            if (count($result) > 0) {
                $msg = 'You cannot delete this imprint; it is used by Series ' . $result[0]['Series_ID'] . '.';
                return $this->jsonDie($msg);
            }
        }
        // Otherwise, treat this as a generic link:
        return $this->handleGenericLink(
            PublishersImprintService::class,
            null,
            null,
            'imprints',
            'getImprintsForPublisher',
            'geeby-deeby/edit-publisher/imprint-list.phtml',
            retrieveLinkMethod: 'getLinkByPublisherAndImprint'
        );
    }

    /**
     * Deal with URIs
     *
     * @return mixed
     */
    public function uriAction()
    {
        $extras = ($pid = $this->params()->fromPost('predicate_id')) ? ['setPredicate' => $pid] : [];
        return $this->handleGenericLink(
            PublishersUriService::class,
            'setPublisher',
            'setUri',
            'uris',
            'getURIsForPublisher',
            'geeby-deeby/edit-publisher/uri-list.phtml',
            $extras,
            retrieveLinkMethod: 'getByPublisherAndUri'
        );
    }
}
