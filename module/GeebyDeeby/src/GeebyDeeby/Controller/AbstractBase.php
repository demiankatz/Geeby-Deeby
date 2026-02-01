<?php

/**
 * Abstract base controller
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

use GeebyDeeby\Db\Entity\EntityInterface;
use GeebyDeeby\Db\Entity\UserEntityInterface;
use GeebyDeeby\Db\Service\DbServiceInterface;
use GeebyDeeby\Db\Service\UserService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Model\ViewModel;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

use function func_get_args;
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
 *
 * @method Plugin\Followup followup() Followup plugin
 */
class AbstractBase extends AbstractActionController
{
    /**
     * Service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

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
     * Get a database service.
     *
     * @param class-string<T> $name Name of service to retrieve
     *
     * @template T
     *
     * @return T
     */
    protected function getDbService(string $name): DbServiceInterface
    {
        return $this->serviceLocator->get(\GeebyDeeby\Db\Service\PluginManager::class)->get($name);
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
     * Is the provided service name a database service?
     *
     * @param string $name Service name to check
     *
     * @return bool
     */
    protected function isDatabaseService(string $name): bool
    {
        return str_starts_with($name, 'GeebyDeeby\Db\Service');
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
     * @param string $serviceName Service name to load list from
     * @param string $assignTo    View variable to assign list to
     * @param string $tpl         Template to use in AJAX mode
     * @param string $permission  Permission to check
     *
     * @return ViewModel
     */
    protected function getGenericList(
        string $serviceName,
        string $assignTo,
        string $tpl,
        string $permission = 'Content_Editor'
    ): ViewModel {
        $ok = $this->checkPermission($permission);
        if ($ok !== true) {
            return $ok;
        }
        $service = $this->isDatabaseService($serviceName)
            ? $this->getDbService($serviceName)
            : $this->getDbTable($serviceName);
        $view = $this->createViewModel([$assignTo => $service->getList()]);

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
            $view->setTemplate($tpl);
        }

        return $view;
    }

    /**
     * Is the type a database entity?
     *
     * @param ReflectionParameter $param Parameter to check
     *
     * @return bool
     */
    protected function isEntityType(ReflectionParameter $param): bool
    {
        $paramType = $param->getType();
        if ($paramType instanceof ReflectionUnionType) {
            $types = $paramType->getTypes();
            foreach ($types as $type) {
                if (is_subclass_of($type->getName(), EntityInterface::class)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Is the type a nullable int?
     *
     * @param ReflectionParameter $param Parameter to check
     *
     * @return bool
     */
    protected function isNullableIntType(ReflectionParameter $param): bool
    {
        $paramType = $param->getType();
        return $paramType instanceof ReflectionNamedType
            && $paramType->getName() === 'int'
            && $paramType->allowsNull();
    }

    /**
     * Cast input value to an appropriate type based on examination of the setter method.
     *
     * @param string          $value           Value to format
     * @param ReflectionClass $reflectionClass Reflection of entity class
     * @param string          $method          Method to check
     *
     * @return bool
     */
    protected function autocastType(string $value, ReflectionClass $reflectionClass, string $method): string|int|null
    {
        $reflectionMethod = $reflectionClass->getMethod($method);
        $firstParam = $reflectionMethod->getParameters()[0];

        // Handle IDs and nullable ints intelligently: empty value should be treated as null and
        // other values should be converted to integers!
        if ($this->isEntityType($firstParam) || $this->isNullableIntType($firstParam)) {
            return empty($value) ? null : intval($value);
        }
        return $value;
    }

    /**
     * Support method for handleGenericItem() -- save.
     *
     * @param string $serviceName Service name to load list from
     * @param array  $assignMap   Map of POST fields => object properties for saving
     * @param string $idField     POST/Route field for unique ID
     *
     * @return mixed
     */
    protected function saveGenericItem(string $serviceName, array $assignMap, string $idField = 'id')
    {
        // Extract values from the POST fields:
        $id = $this->params()->fromRoute(
            $idField,
            $this->params()->fromPost($idField, 'NEW')
        );
        $id = $id == 'NEW' ? false : intval($id);

        // Attempt to save changes:
        $service = $this->getDbService($serviceName);
        $entity = $id === false ? $service->createEntity() : $service->getByPrimaryKey($id);
        if (!is_object($entity)) {
            return $this->jsonDie('Problem loading row');
        }
        $reflectionClass = new ReflectionClass($entity);
        foreach ($assignMap as $post => $method) {
            $value = trim($this->params()->fromPost($post));
            $entity->$method($this->autocastType($value, $reflectionClass, $method));
        }
        $problem = is_callable([$service, 'getValidationError'])
            ? $service->getValidationError($entity)
            : "$serviceName is missing getValidationError implementation.";
        if ($problem) {
            return $this->jsonDie($problem);
        }
        try {
            $service->persistEntity($entity);
        } catch (\Exception $e) {
            return $this->jsonDie('Problem saving changes: ' . $e->getMessage());
        }

        // If we made it this far, we can report success:
        $view = $this->jsonReportSuccess();
        $view->affectedEntity = $entity;
        return $view;
    }

    /**
     * Support method for handleGenericItem() -- delete record.
     *
     * @param string $serviceName Database service to delete item from.
     *
     * @return mixed
     */
    protected function deleteGenericItem($serviceName)
    {
        try {
            $id = $this->params()->fromRoute('id');
            $service = $this->getDbService($serviceName);
            if (!is_callable([$service, 'getByPrimaryKey'])) {
                throw new \Exception('Cannot retrieve entities from ' . $service);
            }
            $entity = $service->getByPrimaryKey($id);
            $service->deleteEntity($entity);
        } catch (\Exception $e) {
            return $this->jsonDie($e->getMessage());
        }
        $view = $this->jsonReportSuccess();
        $view->affectedEntity = $entity;
        return $view;
    }

    /**
     * Support method for handleGenericItem() -- show form.
     *
     * @param string $serviceName Database service to load item from
     * @param string $assignTo    Variable to assign form data to
     *
     * @return ViewModel
     */
    protected function showGenericItem(string $serviceName, string $assignTo): ViewModel
    {
        $id = $this->params()->fromRoute('id', 'NEW');
        $id = $id == 'NEW' ? false : intval($id);
        $service = $this->getDbService($serviceName);
        if ($id) {
            $entity = $service->getByPrimaryKey($id);
            if ($entity) {
                $row = $entity->toArray();
            } else {
                $id = false;
            }
        }
        if (!$id) {
            $entity = $service->createEntity();
            $key = $entity->getPrimaryKeyColumn();
            $row = [$key[0] => 'NEW'];
        }
        return $this->createViewModel(
            [$assignTo => $row ?? null, 'affectedEntity' => $entity ?? null]
        );
    }

    /**
     * Generic method for handling item edit/save actions. Returns an array with
     * two elements: the view object or response, and a boolean indicating whether
     * or not the user has permission to proceed.
     *
     * @param string $serviceName Service name to load item from
     * @param array  $assignMap   Map of POST fields => object properties for saving
     * @param string $assignTo    Variable to assign form data to (for showing form)
     * @param string $permission  Permission to check
     *
     * @return array
     */
    protected function handleGenericItem(
        string $serviceName,
        array $assignMap,
        string $assignTo,
        string $permission = 'Content_Editor'
    ): array {
        $ok = $this->checkPermission($permission);
        if ($ok !== true) {
            return [$ok, false];
        }
        $useService = $this->isDatabaseService($serviceName);
        if ($this->getRequest()->isPost()) {
            $view = $this->saveGenericItem($serviceName, $assignMap);
        } elseif ($this->getRequest()->isDelete()) {
            $view = $this->deleteGenericItem($serviceName);
        } else {
            $view = $this->showGenericItem($serviceName, $assignTo);
            $view->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return [$view, true];
    }

    /**
     * Handle generic linking between two items.
     *
     * @param string    $tableName                Name of database table to modify
     * @param string    $primaryColumn            Name of database column whose value is in 'id' route parameter
     * @param string    $secondaryColumn          Name of database column whose value is in 'extra' route parameter
     * @param string    $listVariable             Name of view variable for list used when displaying existing links
     * @param string    $listMethod               Name of method on table class to call for list assignment
     * @param string    $listTemplate             Name of template to use for displaying list
     * @param array     $extraFields              Extra fields to insert with the link (optional)
     * @param ?callable $insertCallback           Callback function when inserting a new row
     * @param string    $retrieveLinkMethod       Name of service method to fetch a link using primary/secondary values
     * @param bool      $invertRetrieveLinkParams Should we invert the parameter order on retrieveLinkMethod?
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
        $insertCallback = null,
        string $retrieveLinkMethod = 'retrieveLink',
        bool $invertRetrieveLinkParams = false
    ) {
        $ok = $this->checkPermission('Content_Editor');
        if ($ok !== true) {
            return $ok;
        }
        return $this->isDatabaseService($tableName)
            ? $this->handleGenericLinkForService(...func_get_args())
            : $this->handleGenericLinkForTable(...func_get_args());
    }

    /**
     * Handle generic linking between two items using a database service.
     *
     * @param string    $serviceName              Name of database service to leverage
     * @param ?string   $primarySetter            Name of entity setter whose value is in 'id' route parameter
     * (null to disable creation)
     * @param ?string   $secondarySetter          Name of entity setter whose value is in 'extra' route parameter
     * (null to disable creation)
     * @param string    $listVariable             Name of view variable for list used when displaying existing links
     * @param string    $listMethod               Name of method on table class to call for list assignment
     * @param string    $listTemplate             Name of template to use for displaying list
     * @param array     $extraFields              Extra fields to insert with the link (optional). Values will also be
     * passed to retrieveLinkMethod as params 3+.
     * @param ?callable $insertCallback           Callback function when inserting a new row
     * @param string    $retrieveLinkMethod       Name of service method to fetch a link using primary/secondary values
     * @param bool      $invertRetrieveLinkParams Should we invert the order of params 1-2 on retrieveLinkMethod?
     *
     * @return mixed
     */
    public function handleGenericLinkForService(
        string $serviceName,
        ?string $primarySetter,
        ?string $secondarySetter,
        string $listVariable,
        string $listMethod,
        string $listTemplate,
        array $extraFields = [],
        ?callable $insertCallback = null,
        string $retrieveLinkMethod = 'retrieveLink',
        bool $invertRetrieveLinkParams = false
    ) {
        $primary = $this->params()->fromRoute('id');
        $secondary = $this->params()->fromRoute('extra');
        $service = $this->getDbService($serviceName);
        if (!empty($primary) && !empty($secondary)) {
            try {
                if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
                    if (!$primarySetter || !$secondarySetter) {
                        return $this->jsonDie('Primary and secondary setters must be configured to support PUT/POST');
                    }
                    $entity = $service->createEntity();
                    $entity->$primarySetter($primary);
                    $entity->$secondarySetter($secondary);
                    foreach ($extraFields as $extraSetter => $extraValue) {
                        $entity->$extraSetter($extraValue);
                    }
                    $service->persistEntity($entity);
                    if (is_callable($insertCallback)) {
                        $insertCallback($entity);
                    }
                } elseif ($this->getRequest()->isDelete()) {
                    if (!is_callable([$service, $retrieveLinkMethod])) {
                        return $this->jsonDie("$serviceName lacks $retrieveLinkMethod method");
                    }
                    $link = $invertRetrieveLinkParams
                        ? $service->$retrieveLinkMethod($secondary, $primary, ...array_values($extraFields))
                        : $service->$retrieveLinkMethod($primary, $secondary, ...array_values($extraFields));
                    if (!$link) {
                        return $this->jsonDie("Could not retrieve $serviceName link using $primary / $secondary");
                    }
                    $service->deleteEntity($link);
                } else {
                    return $this->jsonDie('Unexpected method');
                }
                return $this->jsonReportSuccess();
            } catch (\Exception $e) {
                return $this->jsonDie('Problem saving changes: ' . $e->getMessage());
            }
        }

        // If we got this far, display a list:
        $view = $this->createViewModel([$listVariable => $service->$listMethod($primary)]);
        $view->setTemplate($listTemplate);
        $view->setTerminal(true);
        return $view;
    }

    /**
     * Handle generic linking between two items using a legacy table object.
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
     *
     * @deprecated use handleGenericLinkForService()
     */
    public function handleGenericLinkForTable(
        $tableName,
        $primaryColumn,
        $secondaryColumn,
        $listVariable,
        $listMethod,
        $listTemplate,
        $extraFields = [],
        $insertCallback = null
    ) {
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
     * @return mixed
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
            if ($user = $this->getDbService(UserService::class)->getByPrimaryKey($id)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Check if the user has the specified permission.
     *
     * @param UserEntityInterface $user       The user to check.
     * @param string              $permission The name of the permission to check.
     *
     * @return bool              True if action permitted, false otherwise.
     */
    public function userHasPermission(UserEntityInterface $user, string $permission): bool
    {
        $permissions = $user->getUserGroup()?->toArray();
        return !empty($permissions[$permission]);
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
        if (!$this->userHasPermission($user, $permission)) {
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
     * Perform authentication
     *
     * @param string $username Username
     * @param string $password Password
     *
     * @return \GeebyDeeby\Authentication\Adapter
     * @throws \Exception
     */
    protected function getAuthenticationAdapter($username, $password)
    {
        return new \GeebyDeeby\Authentication\Adapter(
            $this->getDbService(UserService::class),
            $username,
            $password
        );
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
