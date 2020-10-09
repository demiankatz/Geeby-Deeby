<?php
/**
 * Edition display view helper
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2017.
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

use GeebyDeeby\Controller\EditionController;

/**
 * Edition display view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ShowEdition extends \Laminas\View\Helper\AbstractHelper
{
    /**
     * Edition controller
     *
     * @var EditionController
     */
    protected $controller;

    /**
     * Constructor
     *
     * @param EditionController $controller Edition controller
     */
    public function __construct(EditionController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Render edition details.
     *
     * @param array $id ID of edition to display.
     *
     * @return string
     */
    public function __invoke($id)
    {
        $view = $this->controller->getViewModelWithEditionAndDetails($id);
        $view->skipTitle = true;
        return '<h2>'
            . $this->view->escapeHtml(
                $this->view->fixtitle($view->edition['Edition_Name'])
            ) . '</h2>'
            . $this->view->partial('geeby-deeby/edition/show', $view);
    }
}
