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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
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
class Edition extends TableAwareGateway
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
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
        $parents = [];
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
        $items = [];
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
     * Create a copy of the current edition.
     *
     * @param array $overrides Fields to override durign copying.
     *
     * @return Edition
     */
    public function copy($overrides = [])
    {
        $table = $this->getDbTable('edition');
        $new = $table->createRow();
        foreach ($this->toArray() as $key => $value) {
            if ($key != 'Edition_ID') {
                $new->$key = $value;
            }
        }
        $new->Edition_Name = 'Copy of ' . $new->Edition_Name;
        foreach ($overrides as $key => $value) {
            $new->$key = $value;
        }
        $new->save();
        $table->copyAssociatedInfo($this, $new);
        return $new;
    }

    /**
     * Get immediate children of this edition.
     *
     * @return mixed
     */
    public function getChildren()
    {
        return $this->getDbTable('edition')->select(
            ['Parent_Edition_ID' => $this->Edition_ID]
        );
    }

    /**
     * Copy attributes from another edition.
     *
     * @param int $editionId Edition to copy from
     *
     * @return void
     */
    public function copyAttributes($editionId)
    {
        $attrTable = $this->getDbTable('editionsattributesvalues');
        $clonable = $this->getDbTable('editionsattribute')->getClonableIds();
        if (count($clonable) == 0) {
            return;
        }
        $callback = function ($select) use ($editionId, $clonable) {
            $select->where->equalTo('Edition_ID', $editionId)
                ->in('Editions_Attribute_ID', $clonable);
        };
        $attribs = $attrTable->select($callback);
        foreach ($attribs as $attr) {
            $arr = (array)$attr;
            $arr['Edition_ID'] = $this->Edition_ID;
            $attrTable->insert($arr);
        }
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
            ['Edition_ID' => $editionId]
        );
        foreach ($credits as $credit) {
            $arr = (array)$credit;
            $arr['Edition_ID'] = $this->Edition_ID;
            $creditTable->insert($arr);
        }
    }

    /**
     * Support function for getNextInSeries / getPreviousInSeries.
     *
     * @param bool $next Get next (true) or previous (false)?
     *
     * @return Edition|null
     */
    protected function getAdjacentInSeries($next)
    {
        if (empty($this->Series_ID)) {
            return null;
        }
        $table = $this->getDbTable('edition');
        $edition = $this->Edition_ID;
        $series = $this->Series_ID;
        $vol = $this->Volume;
        $pos = $this->Position;
        $rep = $this->Replacement_Number;
        $name = $this->Edition_Name;
        $callback = function ($select) use ($edition, $series, $name, $vol, $pos,
            $rep, $next
        ) {
            $select->where->equalTo('Series_ID', $series);
            $select->where->notEqualTo('Edition_ID', $edition);
            $fields = [
                'Volume', 'Position', 'Replacement_Number', 'Edition_Name',
                'Edition_ID'
            ];
            $vals = [$vol, $pos, $rep, $name, $edition];
            $nest = $select->where->NEST;
            for ($i = 0; $i < count($fields); $i++) {
                $clause = $nest->OR->NEST;
                for ($j = 0; $j <= $i; $j++) {
                    if ($j == $i) {
                        if ($next) {
                            $clause->greaterThan($fields[$j], $vals[$j]);
                        } else {
                            $clause->lessThan($fields[$j], $vals[$j]);
                        }
                    } else {
                        $clause->equalTo($fields[$j], $vals[$j]);
                    }
                }
                $clause->UNNEST;
            }
            $nest->UNNEST;
            $select->order(
                $next ? $fields : array_map(
                    function ($i) {
                        return "$i DESC";
                    }, $fields
                )
            );
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
    public function getNextInSeries()
    {
        return $this->getAdjacentInSeries(true);
    }

    /**
     * Get previous edition in series.
     *
     * @return Edition|null
     */
    public function getPreviousInSeries()
    {
        return $this->getAdjacentInSeries(false);
    }

    /**
     * Save
     *
     * @return void
     */
    public function save()
    {
        // Ensure integrity of parent value.
        if (empty($this->Parent_Edition_ID)) {
            $this->Parent_Edition_ID = null;
        }
        if (strlen(trim($this->Position_In_Parent)) == 0) {
            $this->Position_In_Parent = null;
        }
        parent::save();
    }
}
