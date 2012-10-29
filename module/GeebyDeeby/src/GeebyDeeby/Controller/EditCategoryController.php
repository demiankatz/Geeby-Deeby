<?php
/**
 * Edit category controller
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

/**
 * Edit category controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditCategoryController extends AbstractBase
{
    /**
     * Display a list of categories
     *
     * @return mixed
     */
    public function listAction()
    {
        $table = $this->getDbTable('category');
        $view = $this->createViewModel(array('categories' => $table->getList()));

        // If this is an AJAX request, render the core list only, not the
        // framing layout and buttons.
        if ($this->getRequest()->isXmlHttpRequest()) {
            $view->setTerminal(true);
            $view->setTemplate('geeby-deeby/edit-category/render-categories');
        }

        return $view;
    }

    /**
     * Save a modified category
     *
     * @return mixed
     */
    protected function saveCategory()
    {
        // Extract values from the POST fields:
        $id = $this->params()->fromPost('id', 'NEW');
        $id = $id == 'NEW' ? false : intval($id);
        $name = trim($this->params()->fromPost('name'));
        $desc = trim($this->params()->fromPost('desc'));

        // Validate input:
        if (empty($name)) {
            return $this->jsonDie('Category name cannot be blank.');
        }

        // Attempt to save changes:
        $table = $this->getDbTable('category');
        $category = $id === false
            ? $table->createRow()
            : $table->select(array('Category_ID' => $id))->current();
        if (!is_object($category)) {
            return $this->jsonDie('Problem loading category');
        }
        $category->Category = $name;
        $category->Description = $desc;
        try {
            $category->save();
        } catch (\Exception $e) {
            return $this->jsonDie('Problem saving changes: ' . $e->getMessage());
        }

        // If we made it this far, we can report success:
        return $this->jsonReportSuccess();
    }

    /**
     * Show a category edit form
     *
     * @return mixed
     */
    protected function showCategoryForm()
    {
        $id = $this->params()->fromRoute('id', 'NEW');
        $id = $id == 'NEW' ? false : intval($id);
        if ($id) {
            $table = $this->getDbTable('category');
            $category = $table->select(array('Category_ID' => $id))->current();
            if (is_object($category)) {
                $row = $category->toArray();
            } else {
                $id = false;
            }
        }
        if (!$id) {
            $row = array('Category_ID' => 'NEW');
        }
        return $this->createViewModel(array('category' => $row));
    }

    /**
     * Operate on a single category
     *
     * @return mixed
     */
    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            $view = $this->saveCategory();
        } else {
            $view = $this->showCategoryForm();
            $view->setTerminal($this->getRequest()->isXmlHttpRequest());
        }
        return $view;
    }
}
