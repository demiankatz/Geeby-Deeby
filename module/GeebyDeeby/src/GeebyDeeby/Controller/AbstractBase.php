<?php

/**
 * Abstract base controller
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

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Model\ViewModel;

use function intval;
use function is_callable;
use function is_object;

/**
 * Abstract base controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AbstractBase extends AbstractActionController
{
    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $sm Service Manager
     */
    public function __construct($sm)
    {
        $this->serviceLocator = $sm;
    }

    /**
     * Provide a fresh view model.
     *
     * @param array $params Parameters to pass to ViewModel constructor.
     *
     * @return ViewModel
     */
    protected function createViewModel($params = null)
    {
        $view = new ViewModel();
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $view->setVariable($k, $v);
            }
        }
        return $view;
    }

    /**
     * Get a database table gateway.
     *
     * @param string $table Name of table service to pull
     *
     * @return \Laminas\Db\TableGateway\AbstractTableGateway
     */
    protected function getDbTable($table)
    {
        return $this->serviceLocator->get('GeebyDeeby\Db\Table\PluginManager')
            ->get(strtolower($table));
    }

    /**
     * Die with a JSON-encoded message.
     *
     * @param string $msg     The message to send back.
     * @param bool   $success Success status
     *
     * @return mixed
     */
    protected function jsonDie($msg, $success = false)
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine(
            'Content-type',
            'application/javascript'
        );
        $headers->addHeaderLine(
            'Cache-Control',
            'no-cache, must-revalidate'
        );
        $headers->addHeaderLine(
            'Expires',
            'Mon, 26 Jul 1997 05:00:00 GMT'
        );
        $output = ['success' => $success, 'msg' => $msg];
        $response->setContent(json_encode($output));
        return $response;
    }

    /**
     * Die with a JSON success status.
     *
     * @return mixed
     */
    protected function jsonReportSuccess()
    {
        return $this->jsonDie('Success', true);
    }

    /**
     * Generic method for displaying a list of items.
     *
     * @param string $table      Table to load list from
     * @param string $assignTo   View variable to assign list to
     * @param string $tpl        Template to use in AJAX mode
     * @param string $permission Permission to check
     *
     * @return mixed
     */
    protected function getGenericList(
        $table,
        $assignTo,
        $tpl,
        $permission = 'Content_Editor'
    ) {
        $ok = $this->checkPermission($permission);
        if ($ok !== true) {
            return $ok;
        }
        $table = $this->getDbTable($table);
        $view = $this->createViewModel([$assignTo => $table->getList()]);

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
            $view->setTemplate($tpl);
        }

        return $view;
    }

    /**
     * Support method for handleGenericItem() -- save.
     *
     * @param string $table     Table to load item from
     * @param array  $assignMap Map of POST fields => object properties for saving
     * @param string $idField   POST/Route field for unique ID
     *
     * @return mixed
     */
    protected function saveGenericItem($table, $assignMap, $idField = 'id')
    {
        // Extract values from the POST fields:
        $id = $this->params()->fromRoute(
            $idField,
            $this->params()->fromPost($idField, 'NEW')
        );
        $id = $id == 'NEW' ? false : intval($id);

        // Attempt to save changes:
        $table = $this->getDbTable($table);
        $row = $id === false ? $table->createRow() : $table->getByPrimaryKey($id);
        if (!is_object($row)) {
            return $this->jsonDie('Problem loading row');
        }
        foreach ($assignMap as $post => $attr) {
            $row->$attr = trim($this->params()->fromPost($post));
            // Handle IDs intelligently: empty value should be treated as null and
            // other values should be converted to integers!
            if (substr($attr, -3) == '_ID') {
                $row->$attr = empty($row->$attr) ? null : intval($row->$attr);
            }
        }
        $problem = $row->validate();
        if ($problem !== false) {
            return $this->jsonDie($problem);
        }
        try {
            $row->save();
        } catch (\Exception $e) {
            return $this->jsonDie('Problem saving changes: ' . $e->getMessage());
        }

        // If we made it this far, we can report success:
        $view = $this->jsonReportSuccess();
        $view->affectedRow = $row;
        return $view;
    }

    /**
     * Support method for handleGenericItem() -- delete record.
     *
     * @param string $table Table to delete item from.
     *
     * @return mixed
     */
    protected function deleteGenericItem($table)
    {
        try {
            $id = $this->params()->fromRoute('id');
            $table = $this->getDbTable($table);
            $rowObj = $table->getByPrimaryKey($id);
            $rowObj->delete();
        } catch (\Exception $e) {
            return $this->jsonDie($e->getMessage());
        }
        return $this->jsonReportSuccess();
    }

    /**
     * Support method for handleGenericItem() -- show form.
     *
     * @param string $table    Table to load item from
     * @param string $assignTo Variable to assign form data to
     *
     * @return mixed
     */
    protected function showGenericItem($table, $assignTo)
    {
        $id = $this->params()->fromRoute('id', 'NEW');
        $id = $id == 'NEW' ? false : intval($id);
        $table = $this->getDbTable($table);
        if ($id) {
            $rowObj = $table->getByPrimaryKey($id);
            if (is_object($rowObj)) {
                $row = $rowObj->toArray();
            } else {
                $id = false;
            }
        }
        if (!$id) {
            $rowObj = $table->createRow();
            $key = $rowObj->getPrimaryKeyColumn();
            $row = [$key[0] => 'NEW'];
        }
        return $this->createViewModel(
            [$assignTo => $row, $assignTo . 'Obj' => $rowObj]
        );
    }

    /**
     * Generic method for handling item edit/save actions. Returns an array with
     * two elements: the view object or response, and a boolean indicating whether
     * or not the user has permission to proceed.
     *
     * @param string $table      Table to load item from
     * @param array  $assignMap  Map of POST fields => object properties for saving
     * @param string $assignTo   Variable to assign form data to (for showing form)
     * @param string $permission Permission to check
     *
     * @return array
     */
    protected function handleGenericItem(
        $table,
        $assignMap,
        $assignTo,
        $permission = 'Content_Editor'
    ) {
        $ok = $this->checkPermission($permission);
        if ($ok !== true) {
            return [$ok, false];
        }
        if ($this->getRequest()->isPost()) {
            $view = $this->saveGenericItem($table, $assignMap);
        } elseif ($this->getRequest()->isDelete()) {
            $view = $this->deleteGenericItem($table);
        } else {
            $view = $this->showGenericItem($table, $assignTo);
            $view->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return [$view, true];
    }

    /**
     * Handle generic linking between two items.
     *
     * @param string   $tableName       Name of database table to modify
     * @param string   $primaryColumn   Name of database column whose value is in
     * 'id' route parameter
     * @param string   $secondaryColumn Name of database column whose value is in
     * 'extra' route parameter
     * @param string   $listVariable    Name of view variable to assign list to
     * when displaying existing links
     * @param string   $listMethod      Name of method on table class to call for
     * list assignment
     * @param string   $listTemplate    Name of template to use for displaying list
     * @param array    $extraFields     Extra fields to insert with the link
     * (optional)
     * @param Callback $insertCallback  Callback function when inserting a new row
     *
     * @return mixed
     */
    public function handleGenericLink(
        $tableName,
        $primaryColumn,
        $secondaryColumn,
        $listVariable,
        $listMethod,
        $listTemplate,
        $extraFields = [],
        $insertCallback = null
    ) {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        $primary = $this->params()->fromRoute('id');
        $secondary = $this->params()->fromRoute('extra');
        $table = $this->getDbTable($tableName);
        if (!empty($primary) && !empty($secondary)) {
            $row = [$primaryColumn => $primary, $secondaryColumn => $secondary];
            $row += $extraFields;
            try {
                if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
                    $table->insert($row);
                    if (is_callable($insertCallback)) {
                        $insertCallback(
                            $table->getLastInsertValue(),
                            $row,
                            $this->serviceLocator
                        );
                    }
                } elseif ($this->getRequest()->isDelete()) {
                    $table->delete($row);
                } else {
                    return $this->jsonDie('Unexpected method');
                }
                return $this->jsonReportSuccess();
            } catch (\Exception $e) {
                return $this->jsonDie('Problem saving changes: ' . $e->getMessage());
            }
        }

        // If we got this far, display a list:
        $view = $this->createViewModel();
        $view->$listVariable = $table->$listMethod($primary);
        $view->setTemplate($listTemplate);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Get access to the authentication service.
     *
     * @return \Laminas\Authentication\AuthenticationService
     */
    protected function getAuth()
    {
        return $this->serviceLocator->get('GeebyDeeby\Authentication');
    }

    /**
     * Convenience method to make invocation of forward() helper less verbose.
     *
     * @param string $controller Controller to invoke
     * @param string $action     Action to invoke
     * @param array  $params     Extra parameters for the RouteMatch object (no
     * need to provide action here, since $action takes care of that)
     *
     * @return bool              Returns false so this can be returned by a
     * controller without causing duplicate ViewModel attachment.
     */
    public function forwardTo($controller, $action, $params = [])
    {
        // Inject action into the RouteMatch parameters
        $params['action'] = $action;

        // Dispatch the requested controller/action:
        return $this->forward()->dispatch($controller, $params);
    }

    /**
     * Get the view renderer
     *
     * @return \Laminas\View\Renderer\RendererInterface
     */
    protected function getViewRenderer()
    {
        return $this->serviceLocator->get('ViewRenderer');
    }

    /**
     * Get the full URL to a route.
     *
     * @param bool|string $route       Boolean true for current URL, otherwise name
     * of route to render as URL
     * @param array       $routeParams Route parameters (optional)
     *
     * @return string
     */
    public function getServerUrl($route = true, $routeParams = [])
    {
        $serverHelper = $this->getViewRenderer()->plugin('serverurl');
        return $serverHelper(
            $route === true ? true : $this->url()->fromRoute($route, $routeParams)
        );
    }

    /**
     * Get the current user (if logged in) or false.
     *
     * @return \GeebyDeeby\Db\Row\User|bool
     */
    protected function getCurrentUser()
    {
        if ($this->getAuth()->hasIdentity()) {
            $id = $this->getAuth()->getIdentity();
            $user = $this->getDbTable('user')->getByPrimaryKey($id);
            if (is_object($user)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Check that a user is logged in and has appropriate permissions.
     * Returns boolean true if user has permission; otherwise returns a
     * response object to redirect appropriately.
     *
     * @param string $permission Permission to check for
     *
     * @return mixed
     */
    protected function checkPermission($permission)
    {
        if (!($user = $this->getCurrentUser())) {
            return $this->forceLogin();
        }
        if (!$user->hasPermission($permission)) {
            return $this->forwardTo('GeebyDeeby\Controller\Edit', 'Denied');
        }
        return true;
    }

    /**
     * Redirect the user to the login screen.
     *
     * @param array $extras  Associative array of extra fields to store
     * @param bool  $forward True to forward, false to redirect
     *
     * @return mixed
     */
    protected function forceLogin($extras = [], $forward = true)
    {
        $this->followup()->store($extras);

        // Set a flag indicating that we are forcing login:
        $this->getRequest()->getPost()->set('forcingLogin', true);

        return $forward
            ? $this->forwardTo('GeebyDeeby\Controller\Index', 'Login')
            : $this->redirect()->toRoute('login');
    }

    /**
     * Format an RDF response.
     *
     * @param \EasyRdf\Graph $graph Graph to output
     *
     * @return mixed
     */
    protected function getRdfResponse(\EasyRdf\Graph $graph)
    {
        $requestedFormat = $this->rdfRequested(true);
        switch ($requestedFormat) {
            case 'application/x-turtle':
            case 'text/turtle':
                $serialization = 'turtle';
                break;
            case 'application/rdf+xml':
                $serialization = 'rdfxml';
                break;
            default:
                $serialization = 'ntriples';
                break;
        }

        $response = $this->getResponse();
        $response->setContent($graph->serialise($serialization));
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-type', $requestedFormat);
        return $response;
    }

    /**
     * Should we perform a 303 redirect to RDF? Return preferred RDF format
     * if true, false otherwise.
     *
     * @param bool $force Should we FORCE some kind of RDF format, or allow the
     * possibility of HTML?
     *
     * @return string|bool
     */
    protected function rdfRequested($force = false)
    {
        $accept = $this->getRequest()->getHeaders()->get('accept');
        // Order of preference: earlier items in list are preferred; later
        // items will only be chosen if they're explicitly given a higher
        // priority in the accept headers.
        $rdfForms = [
            'text/turtle', 'application/x-turtle',  // Turtle
            'text/plain', 'application/n-triples',  // N-Triples
            'application/rdf+xml',                  // RDF-XML
        ];
        $bestMatch = $force ? -1 : $accept->match('text/html')->getPriority();
        $bestFormat = false;            // HTML by default
        foreach ($rdfForms as $current) {
            $currentMatchObject = $accept->match($current);
            $currentMatch = is_object($currentMatchObject)
                ? $currentMatchObject->getPriority() : -1;
            if ($currentMatch > $bestMatch) {
                $bestMatch = $currentMatch;
                $bestFormat = $current;
            }
        }
        return $bestFormat;
    }

    /**
     * Perform a 303 redirect for RDF display.
     *
     * @param string $route   Target route
     * @param string $idParam Route parameter containing ID
     *
     * @return mixed
     */
    protected function performRdfRedirect($route, $idParam = 'id')
    {
        $action = $this->rdfRequested() ? 'RDF' : 'Show';
        $id = $this->params()->fromRoute($idParam);
        $response = $this->redirect()->toRoute(
            $route,
            ['action' => $action, $idParam => $id],
            ['query' => $this->params()->fromQuery()]
        );
        $response->setStatusCode(303);
        return $response;
    }
}
