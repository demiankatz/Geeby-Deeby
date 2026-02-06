<?php

/**
 * Button view helper
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\View\Helper;

use GeebyDeeby\ServiceManager\Factory\Autowire;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\HtmlAttributes;

/**
 * Button view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class IconButton
{
    /**
     * Constructor
     *
     * @param HtmlAttributes $htmlAttributesHelper HtmlAttributes view helper
     * @param EscapeHtml     $escapeHtmlHelper     EscapeHtml view helper
     */
    public function __construct(
        #[Autowire(container: 'ViewHelperManager')]
        protected HtmlAttributes $htmlAttributesHelper,
        #[Autowire(container: 'ViewHelperManager')]
        protected EscapeHtml $escapeHtmlHelper
    ) {
    }

    /**
     * Create a button control.
     *
     * @param string $type            Type of button
     * @param string $action          Javascript for button to execute
     * @param string $label           Screen reader label
     * @param array  $extraAttributes Extra attributes to include in button tag
     *
     * @return string
     */
    public function __invoke(string $type, string $action, string $label, array $extraAttributes = []): string
    {
        $extraAttributes['onclick'] = $action;
        $attributes = ($this->htmlAttributesHelper)($extraAttributes);
        $safeLabel = ($this->escapeHtmlHelper)($label);
        return <<<HTML
            <button $attributes>
              <span class="ui-icon ui-icon-$type">
              </span>
              <span class="sr-only">$safeLabel</span>
            </button>
            HTML;
    }
}
