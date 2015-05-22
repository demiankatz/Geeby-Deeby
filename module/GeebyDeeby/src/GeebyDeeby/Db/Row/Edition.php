<?php
/**
 * Row Definition for Editions
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\Db\Row;

/**
 * Row Definition for Editions
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Edition extends ServiceLocatorAwareGateway
{
    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Edition_ID', 'Editions', $adapter);
    }

    /**
     * Validate the fields in the current object.  Return error message if problem
     * found, boolean false if no errors were found.
     *
     * @return string|bool
     */
    public function validate()
    {
        if (empty($this->Edition_Name)) {
            return 'Edition name cannot be blank.';
        }
        if (in_array($this->Edition_ID, $this->getEditionParentChain())) {
            return 'Edition can not be its own parent or grandparent.';
        }
        if (!empty($this->Item_ID)
            && in_array($this->Item_ID, $this->getItemParentChain())
        ) {
            return 'Item can not be its own parent or grandparent.';
        }
        return false;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->Edition_Name;
    }

    /**
     * Get an array of all parent Edition IDs.
     *
     * @return array
     */
    public function getEditionParentChain()
    {
        $parents = array();
        $nextParent = $this->Parent_Edition_ID;
        $table = $this->getDbTable('edition');
        while (true) {
            // Circular parent detection:
            if (empty($nextParent) || in_array($nextParent, $parents)) {
                return $parents;
            }
            $parents[] = $nextParent;
            $nextParent = $table->getByPrimaryKey($nextParent)->Parent_Edition_ID;
        }
    }

    /**
     * Get an array of all parent Item IDs.
     *
     * @return array
     */
    public function getItemParentChain()
    {
        $editions = $this->getEditionParentChain();
        $items = array();
        $table = $this->getDbTable('edition');
        foreach ($editions as $edition) {
            $obj = $table->getByPrimaryKey($edition);
            if (!empty($obj->Item_ID)) {
                $items[] = $obj->Item_ID;
            }
        }
        return $items;
    }

    /**
     * Copy credits from another edition.
     *
     * @param int $editionId Edition to copy from
     *
     * @return void
     */
    public function copyCredits($editionId)
    {
        $creditTable = $this->getDbTable('editionscredits');
        $credits = $creditTable->select(
            array('Edition_ID' => $editionId)
        );
        foreach ($credits as $credit) {
            $arr = (array)$credit;
            $arr['Edition_ID'] = $this->Edition_ID;
            $creditTable->insert($arr);
        }
    }

    /**
     * Get previous edition in series.
     *
     * @return Edition|null
     */
    public function getNextInSeries()
    {
        if (empty($this->Series_ID)) {
            return null;
        }
        $table = $this->getDbTable('edition');
        $edition = $this->Edition_ID;
        $series = $this->Series_ID;
        $pos = $this->Position;
        $name = $this->Edition_Name;
        $callback = function ($select) use ($edition, $series, $name, $pos) {
            $select->where->equalTo('Series_ID', $series);
            $select->where->notEqualTo('Edition_ID', $edition);
            $nest = $select->where->NEST;
            $nest->greaterThan('Position', $pos);
            $nest2 = $nest->OR->NEST;
            $nest2->equalTo('Position', $pos);
            $nest2->greaterThan('Edition_Name', $name);
            $nest2->UNNEST;
            $nest3 = $nest->OR->NEST;
            $nest3->equalTo('Position', $pos);
            $nest3->equalTo('Edition_Name', $name);
            $nest3->greaterThan('Edition_ID', $edition);
            $nest3->UNNEST;
            $nest->UNNEST;
            $select->order(array('Position', 'Edition_Name', 'Edition_ID'));
            $select->limit(1);
        };
        $results = $table->select($callback);
        return count($results) > 0 ? $results->current() : null;
    }

    /**
     * Get previous edition in series.
     *
     * @return Edition|null
     */
    public function getPreviousInSeries()
    {
        if (empty($this->Series_ID)) {
            return null;
        }
        $table = $this->getDbTable('edition');
        $edition = $this->Edition_ID;
        $series = $this->Series_ID;
        $pos = $this->Position;
        $name = $this->Edition_Name;
        $callback = function ($select) use ($edition, $series, $name, $pos) {
            $select->where->equalTo('Series_ID', $series);
            $select->where->notEqualTo('Edition_ID', $edition);
            $nest = $select->where->NEST;
            $nest->lessThan('Position', $pos);
            $nest2 = $nest->OR->NEST;
            $nest2->equalTo('Position', $pos);
            $nest2->lessThan('Edition_Name', $name);
            $nest2->UNNEST;
            $nest3 = $nest->OR->NEST;
            $nest3->equalTo('Position', $pos);
            $nest3->equalTo('Edition_Name', $name);
            $nest3->lessThan('Edition_ID', $edition);
            $nest3->UNNEST;
            $nest->UNNEST;
            $select->order(
                array('Position DESC', 'Edition_Name DESC', 'Edition_ID DESC')
            );
            $select->limit(1);
        };
        $results = $table->select($callback);
        return count($results) > 0 ? $results->current() : null;
    }
}
