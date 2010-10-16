<?php
/**
  *
  * Copyright (c) Demian Katz 2010.
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
require_once 'Gamebooks/AJAX/Edit/Base.php';
require_once 'Gamebooks/Tables/Link.php';
require_once 'Gamebooks/Tables/LinkType.php';

/**
 * Edit AJAX Support
 *
 * This class provides all AJAX functionality for the edit module.
 *
 * @author      Demian Katz
 * @access      public
 */
class AJAX_Edit_Link extends AJAX_Edit_Base
{
    /**
     * Display "edit link" form.
     *
     * @access  public
     */
    public function edit()
    {
        if (is_numeric($_GET['id'])) {
            $link = new Link($_GET['id']);
            $row = $link->getRow();
            if (!$row) {
                die('Cannot load link');
            }
            $this->interface->assign('link', $row);
        } else {
            $this->interface->assign('link', array('Link_ID' => 'NEW'));
        }
        $types = new LinkTypeList();
        $types->assign($this->interface);
        $this->interface->showSubPage('link_edit.tpl');
    }
    
    /**
     * Display link list.
     *
     * @access  public
     */
    public function getList()
    {
        $ll = new LinkList();
        $ll->assign($this->interface);
        $this->interface->showSubPage('link_list.tpl');
    }

    /**
     * Get a list of related people.
     *
     * @access  public
     */
    public function getPersonList()
    {
        require_once 'Gamebooks/Tables/Person.php';
        $people = new PersonList();
        $this->interface->assign('peopleLinks', $people->getByLink($_GET['id']));
        $this->interface->showSubPage('link_person_list.tpl');
    }

    /**
     * Get a list of related items.
     *
     * @access  public
     */
    public function getItemList()
    {
        require_once 'Gamebooks/Tables/Item.php';
        $items = new ItemList();
        $this->interface->assign('itemLinks', $items->getByLink($_GET['id']));
        $this->interface->showSubPage('link_item_list.tpl');
    }

    /**
     * Get a list of related series.
     *
     * @access  public
     */
    public function getSeriesList()
    {
        require_once 'Gamebooks/Tables/Series.php';
        $series = new SeriesList();
        $this->interface->assign('seriesLinks', $series->getByLink($_GET['id']));
        $this->interface->showSubPage('link_series_list.tpl');
    }

    /**
     * Associate an item.
     *
     * @access  public
     */
    public function linkItem()
    {
        $link_id = intval($_POST['link_id']);
        $item_id = intval($_POST['item_id']);
        $link = new Link($link_id);
        if ($link->linkItem($item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove an item association.
     *
     * @access  public
     */
    public function unlinkItem()
    {
        $link_id = intval($_POST['link_id']);
        $item_id = intval($_POST['item_id']);
        $link = new Link($link_id);
        if ($link->unlinkItem($item_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Associate a series.
     *
     * @access  public
     */
    public function linkSeries()
    {
        $link_id = intval($_POST['link_id']);
        $series_id = intval($_POST['series_id']);
        $link = new Link($link_id);
        if ($link->linkSeries($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove a series association.
     *
     * @access  public
     */
    public function unlinkSeries()
    {
        $link_id = intval($_POST['link_id']);
        $series_id = intval($_POST['series_id']);
        $link = new Link($link_id);
        if ($link->unlinkSeries($series_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Associate a person.
     *
     * @access  public
     */
    public function linkPerson()
    {
        $link_id = intval($_POST['link_id']);
        $person_id = intval($_POST['person_id']);
        $link = new Link($link_id);
        if ($link->linkPerson($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem storing relationship.');
        }
    }

    /**
     * Remove a person association.
     *
     * @access  public
     */
    public function unlinkPerson()
    {
        $link_id = intval($_POST['link_id']);
        $person_id = intval($_POST['person_id']);
        $link = new Link($link_id);
        if ($link->unlinkPerson($person_id)) {
            $this->jsonReportSuccess();
        } else {
            $this->jsonDie('Problem removing relationship.');
        }
    }
    
    /**
     * Save changes to a link.
     *
     * @access  public
     */
    public function save()
    {
        // Extract values from the POST fields:
        $id = $_POST['id'] == 'NEW' ? false : intval($_POST['id']);
        $name = trim($_POST['link_name']);
        $url = trim($_POST['url']);
        $desc = trim($_POST['desc']);
        $checked = trim($_POST['date_checked']);
        $type_id = intval($_POST['type_id']);
        
        // Validate input:
        if (empty($name)) {
            $this->jsonDie('Link name cannot be blank.');
        }
        if (empty($url)) {
            $this->jsonDie('URL cannot be blank.');
        }
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $checked)) {
            $this->jsonDie('Date must match YYYY-MM-DD format.');
        }
        
        // Attempt to save changes:
        $link = new Link($id);
        $link->set('Link_Name', $name);
        $link->set('URL', $url);
        $link->set('Description', $desc);
        $link->set('Date_Checked', $checked);
        $link->set('Link_Type_ID', $type_id);
        if (!$link->save()) {
            $this->jsonDie('Problem saving changes.');
        }
        
        // If we made it this far, we can report success:
        $this->jsonReportSuccess();
    }
}
?>