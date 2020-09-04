<?php
/**
 * Edit publisher controller
 *
 * PHP version 5
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;

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
            'publisher', 'publishers', 'geeby-deeby/edit-publisher/render-publishers'
        );
    }

    /**
     * Operate on a single platform
     *
     * @return mixed
     */
    public function indexAction()
    {
        $assignMap = ['publisher' => 'Publisher_Name'];
        $view = $this->handleGenericItem('publisher', $assignMap, 'publisher');
        // Add extra fields/controls if outside of a lightbox:
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $view->cities = $this->getDbTable('city')->getList();
            $view->countries = $this->getDbTable('country')->getList();
            $view->addresses = $this->getDbTable('publishersaddresses')
                ->getAddressesForPublisher($view->publisherObj->Publisher_ID);
            $view->imprints = $this->getDbTable('publishersimprints')
                ->getImprintsForPublisher($view->publisherObj->Publisher_ID);
            $view->uris = $this->getDbTable('publishersuris')
                ->getURIsForCity($view->publisherObj->Publisher_ID);
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
            $table = $this->getDbTable('publishersaddresses');
            $row = $table->createRow();
            $row->Publisher_ID = $this->params()->fromRoute('id');
            $row->Country_ID = $this->params()->fromPost('country');
            $row->City_ID = $this->params()->fromPost('city');
            if (empty($row->City_ID)) {
                $row->City_ID = null;
            }
            $row->Street = $this->params()->fromPost('street');
            if (empty($row->Country_ID)) {
                return $this->jsonDie('Country must be specified.');
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Prevent deletion of imprints that are linked up:
            if ($this->getRequest()->isDelete()) {
                $extra = $this->params()->fromRoute('extra');
                $result = $this->getDbTable('seriespublishers')->select(
                    ['Address_ID' => $extra]
                );
                if (count($result) > 0) {
                    $row = $result->current();
                    $msg = 'You cannot delete this address; it is used by Series '
                        . $row->Series_ID . '.';
                    return $this->jsonDie($msg);
                }
            }
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'publishersaddresses', 'Publisher_ID', 'Address_ID',
                'addresses', 'getAddressesForPublisher',
                'geeby-deeby/edit-publisher/address-list.phtml'
            );
        }
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
            $table = $this->getDbTable('publishersimprints');
            $row = $table->createRow();
            $row->Publisher_ID = $this->params()->fromRoute('id');
            $row->Imprint_Name = $this->params()->fromPost('imprint');
            if (empty($row->Imprint_Name)) {
                return $this->jsonDie('Name must not be empty.');
            }
            $table->insert((array)$row);
            return $this->jsonReportSuccess();
        } else {
            // Prevent deletion of imprints that are linked up:
            if ($this->getRequest()->isDelete()) {
                $extra = $this->params()->fromRoute('extra');
                $result = $this->getDbTable('seriespublishers')->select(
                    ['Imprint_ID' => $extra]
                );
                if (count($result) > 0) {
                    $row = $result->current();
                    $msg = 'You cannot delete this imprint; it is used by Series '
                        . $row->Series_ID . '.';
                    return $this->jsonDie($msg);
                }
            }
            // Otherwise, treat this as a generic link:
            return $this->handleGenericLink(
                'publishersimprints', 'Publisher_ID', 'Imprint_ID',
                'imprints', 'getImprintsForPublisher',
                'geeby-deeby/edit-publisher/imprint-list.phtml'
            );
        }
    }

    /**
     * Deal with URIs
     *
     * @return mixed
     */
    public function uriAction()
    {
        $extras = ($pid = $this->params()->fromPost('predicate_id'))
            ? ['Predicate_ID' => $pid] : [];
        return $this->handleGenericLink(
            'publishersuris', 'Publisher_ID', 'URI',
            'uris', 'getURIsForPublisher',
            'geeby-deeby/edit-publisher/uri-list.phtml',
            $extras
        );
    }
}
