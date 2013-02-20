<?php
/**
 * Person controller
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
 * Person controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PersonController extends AbstractBase
{
    /**
     * "Show person" page
     *
     * @return mixed
     */
    public function indexAction()
    {
        $id = $this->params()->fromRoute('id');
        if (null === $id) {
            return $this->forwardTo(__NAMESPACE__ . '\Person', 'list');
        }
        $table = $this->getDbTable('person');
        $rowObj = $table->getByPrimaryKey($id);
        if (!is_object($rowObj)) {
            return $this->forwardTo(__NAMESPACE__ . '\Person', 'notfound');
        }
        $view = $this->createViewModel(
            array('person' => $rowObj->toArray())
        );
        $view->credits = $this->getDbTable('itemscredits')->getCreditsForPerson($id);
        $pseudo = $this->getDbTable('pseudonyms');
        $view->pseudonyms = $pseudo->getPseudonyms($id);
        $view->realNames = $pseudo->getRealNames($id);
        $view->files = $this->getDbTable('peoplefiles')->getFilesForPerson($id);
        $view->bibliography = $this->getDbTable('peoplebibliography')
            ->getItemsDescribingPerson($id);
        $view->links = $this->getDbTable('peoplelinks')->getLinksForPerson($id);
        return $view;
    }

    /**
     * Person list
     *
     * @return mixed
     */
    public function listAction()
    {
        $extra = $this->params()->fromRoute('extra');
        $bios = (strtolower($extra) == 'bios');
        return $this->createViewModel(
            array(
                'bioMode' => $bios,
                'people' => $this->getDbTable('person')->getList($bios)
            )
        );
    }

    /**
     * Not found page
     *
     * @return mixed
     */
    public function notfoundAction()
    {
        return $this->createViewModel();
    }
}
