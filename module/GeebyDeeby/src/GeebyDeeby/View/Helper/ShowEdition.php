<?php

/**
 * Edition display view helper
 *
 * PHP version 8
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
 * along with this program; if not, see
 * <https://www.gnu.org/licenses/>.
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\Controller\EditionController;
use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\Partial;

/**
 * Edition display view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class ShowEdition
{
    /**
     * Constructor
     *
     * @param EditionController $controller       Edition controller
     * @param EscapeHtml        $escapeHtmlHelper EscapeHtml view helper
     * @param FixTitle          $fixTitleHelper   FixTitle view helper
     * @param Partial           $partialHelper    Partial view helper
     */
    public function __construct(
        #[Autowire(container: 'ControllerManager')]
        protected EditionController $controller,
        #[Autowire(container: 'ViewHelperManager')]
        protected EscapeHtml $escapeHtmlHelper,
        #[Autowire(container: 'ViewHelperManager')]
        protected FixTitle $fixTitleHelper,
        #[Autowire(container: 'ViewHelperManager')]
        protected Partial $partialHelper,
    ) {
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
            . ($this->escapeHtmlHelper)(
                ($this->fixTitleHelper)($view->edition['Edition_Name'])
            ) . '</h2>'
            . ($this->partialHelper)('geeby-deeby/edition/show', $view);
    }
}
