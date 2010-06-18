<?php
/**
  *
  * Copyright (c) Demian Katz 2009.
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
  */
require_once 'Smarty/libs/Smarty.class.php';
require_once 'Gamebooks/config.php';

/**
 * User Interface Class
 *
 * This class extends the Smarty template class to use some gamebook-specific
 * defaults.
 *
 * @author      Demian Katz
 * @access      public
 */
class UI extends Smarty
{
    private $javascript = array();      // JS files to load
    private $css = array();             // CSS files to load
    private $pageTitle = false;         // Current page title
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string  $templateSet    The template set to use (public or edit)
     */
    public function __construct($templateSet = 'public')
    {
        // Set up defaults:
        parent::__construct();
        
        // Override/add some paths:
        $this->template_dir = GAMEBOOKS_HOME . '/smarty/templates/' . $templateSet;
        $this->compile_dir = GAMEBOOKS_HOME . '/tmp/smarty/compile';
        $this->cache_dir = GAMEBOOKS_HOME . '/tmp/smarty/cache';
        $this->plugins_dir[] = GAMEBOOKS_HOME . '/smarty/plugins';
        
        // Default support files:
        $this->addCSS('ui-lightness/jquery-ui-1.7.2.custom.css');
        $this->addJavascript('jquery.js');
        $this->addJavascript('jquery-ui-1.7.2.custom.min.js');
    }
    
    /** Add a CSS file to the page
     *
     * @access  public
     * @param   string  $css            The name of the CSS file (must be in the
     *                                  standard css directory under 
     *                                  GAMEBOOKS_HOME).
     */
    public function addCSS($css)
    {
        $this->css[] = $css;
    }
    
    /** Add a Javascript file to the page
     *
     * @access  public
     * @param   string  $js             The name of the JS file (must be in the
     *                                  standard js directory under GAMEBOOKS_HOME).
     */
    public function addJavascript($js)
    {
        $this->javascript[] = $js;
    }
    
    /**
     * Set the title of the current page
     *
     * @access  public
     * @param   string  $title          The title of the current page.
     */
    public function setPageTitle($title)
    {
        $this->pageTitle = $title;
    }
    
    /**
     * Show a page to the user
     *
     * @access  public
     * @param   string  $page           The name of the page to display within
     *                                  the current template set.
     */
    public function showPage($page)
    {
        $this->assign('siteEmail', GAMEBOOKS_SITE_EMAIL);
        $this->assign('siteTitle', GAMEBOOKS_SITE_NAME);
        $this->assign('pageTitle', $this->pageTitle);
        $this->assign('css', $this->css);
        $this->assign('js', $this->javascript);
        $this->assign('subPage', $page);
        
        header('content-type: text/html; charset: utf-8');
        $this->display('main.tpl');
    }
    
    /**
     * Show a partial page to the user (for use in AJAX calls)
     *
     * @access  public
     * @param   string  $page           The name of the page to display by itself.
     */
    public function showSubPage($page)
    {
        header('content-type: text/html; charset: utf-8');
        $this->display($page);
    }
}
?>