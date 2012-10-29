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
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
     * @return \Zend\Db\TableGateway\AbstractTableGateway
     */
    protected function getDbTable($table)
    {
        return $this->getServiceLocator()->get('GeebyDeeby\DbTablePluginManager')
            ->get($table);
    }

    /**
     * Die with a JSON-encoded error message.
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
            'Content-type', 'application/javascript'
        );
        $headers->addHeaderLine(
            'Cache-Control', 'no-cache, must-revalidate'
        );
        $headers->addHeaderLine(
            'Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'
        );
        $output = array('success' => $success, 'msg' => $msg);
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
}
