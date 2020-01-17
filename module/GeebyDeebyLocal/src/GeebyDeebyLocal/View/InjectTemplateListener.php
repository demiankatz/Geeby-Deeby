<?php
/**
 * Custom template listener for GeebyDeebyLocal.
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
 * @package  View
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\View;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface as ViewModel;

/**
 * Custom template listener for GeebyDeebyLocal.
 *
 * @category GeebyDeeby
 * @package  View
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class InjectTemplateListener extends \Zend\Mvc\View\Http\InjectTemplateListener
{
    /**
     * Inject a template into the view model, if none present
     *
     * Template is derived from the controller found in the route match, and,
     * optionally, the action, if present.
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function injectTemplate(MvcEvent $e)
    {
        parent::injectTemplate($e);
        $model = $e->getResult();
        if (! $model instanceof ViewModel) {
            return;
        }
        $template = $model->getTemplate();
        // If the template falls in the geeby-deeby-local namespace but
        // doesn't exist, inherit from geeby-deeby instead.
        if (substr($template, 0, 18) == 'geeby-deeby-local/'
            && !file_exists(__DIR__ . '/../../../view/' . $template . '.phtml')
        ) {
            $parts = explode('/', $template);
            $parts[0] = 'geeby-deeby';
            $model->setTemplate(implode('/', $parts));
        }
    }
}