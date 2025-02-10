<?php

/**
 * Table Definition for Collections
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
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Select;

use function is_array;

/**
 * Table Definition for Collections
 *
 * @category GeebyDeeby
 * @package  Db_Table
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Collections extends Gateway
{
    /**
     * Should we disable logging for this class?
     *
     * @var bool
     */
    protected static $doNotLog = true;

    /**
     * Constructor
     *
     * @param Adapter       $adapter Database adapter
     * @param PluginManager $tm      Table manager
     * @param RowGateway    $rowObj  Row prototype object (null for default)
     */
    public function __construct(
        Adapter $adapter,
        PluginManager $tm,
        RowGateway $rowObj = null
    ) {
        parent::__construct($adapter, $tm, $rowObj, 'Collections');
    }

    /**
     * Get a list of users owning/interested in an item.
     *
     * @param int    $itemID Item ID
     * @param string $type   List type ('extra', 'have', 'want' or null for all)
     *
     * @return mixed
     */
    public function getForItem($itemID, $type = null)
    {
        $callback = function ($select) use ($itemID, $type) {
            $select->join(
                ['u' => 'Users'],
                'Collections.User_ID = u.User_ID'
            );
            $select->order(['Username']);
            $select->where->equalTo('Item_ID', $itemID);
            if (null !== $type) {
                $select->where->equalTo('Collection_Status', $type);
            }
        };
        return $this->select($callback);
    }

    /**
     * Get a list of items on a user's collection list(s).
     *
     * @param int          $userID      User ID
     * @param string|array $type        List type ('extra', 'have', 'want' -- or
     * array of these values -- or null for all)
     * @param bool         $groupByLang Should we group by language?
     *
     * @return mixed
     */
    public function getForUser($userID, $type = null, $groupByLang = false)
    {
        $callback = function ($select) use ($userID, $type, $groupByLang) {
            $select->join(
                ['i' => 'Items'],
                'Collections.Item_ID = i.Item_ID'
            );
            $select->join(
                ['eds' => 'Editions'],
                'i.Item_ID = eds.Item_ID'
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->join(
                ['s' => 'Series'],
                'eds.Series_ID = s.Series_ID AND Collections.Series_ID = s.Series_ID'
            );
            if ($groupByLang) {
                $select->join(
                    ['l' => 'Languages'],
                    's.Language_ID = l.Language_ID'
                );
                $fields = [
                    'Language_Name', 'Series_Name', 's.Series_ID',
                    'Collection_Status', 'Volume', 'Position', 'Replacement_Number',
                    'Item_Name',
                ];
            } else {
                $fields = [
                    'Series_Name', 's.Series_ID', 'Collection_Status', 'Volume',
                    'Position', 'Replacement_Number', 'Item_Name',
                ];
            }
            $select->order($fields);
            $select->group($fields + ['i.Item_ID']);
            $select->where->equalTo('Collections.User_ID', $userID);
            if (null !== $type) {
                if (is_array($type)) {
                    $select->where->in('Collection_Status', $type);
                } else {
                    $select->where->equalTo('Collection_Status', $type);
                }
            }
        };
        return $this->select($callback);
    }

    /**
     * Find items from other users' lists that have $desiredStatus and match items on
     * $userID's lists that have $userStatus.
     *
     * @param int    $userID        User ID
     * @param string $userStatus    The user's status that should be matched against
     * $desiredStatus
     * @param string $desiredStatus The status for which to retrieve matching items
     *
     * @return mixed
     */
    public function compareCollections($userID, $userStatus, $desiredStatus)
    {
        $callback = function ($select) use ($userID, $userStatus, $desiredStatus) {
            $select->join(
                ['i' => 'Items'],
                'Collections.Item_ID = i.Item_ID'
            );
            $select->join(
                ['u' => 'Users'],
                'Collections.User_ID = u.User_ID'
            );
            $select->join(
                ['Other' => 'Collections'],
                'Other.Item_ID = Collections.Item_ID AND '
                . 'Other.Series_ID = Collections.Series_ID',
                ['Other_Note' => 'Collection_Note']
            );
            $select->join(
                ['eds' => 'Editions'],
                'i.Item_ID = eds.Item_ID'
            );
            $select->join(
                ['iat' => 'Items_AltTitles'],
                'eds.Preferred_Item_AltName_ID = iat.Sequence_ID',
                ['Item_AltName'],
                Select::JOIN_LEFT
            );
            $select->join(
                ['s' => 'Series'],
                'eds.Series_ID = s.Series_ID AND Collections.Series_ID = s.Series_ID'
            );
            $fields = [
                'Username', 'Series_Name', 's.Series_ID', 'Volume', 'Position',
                'Replacement_Number', 'Item_Name',
            ];
            $select->order($fields);
            $select->group($fields + ['i.Item_ID']);
            $select->where->equalTo('Other.User_ID', $userID);
            $select->where->notEqualTo('Collections.User_ID', $userID);
            $select->where->equalTo('Other.Collection_Status', $userStatus);
            $select->where->equalTo('Collections.Collection_Status', $desiredStatus);
        };
        return $this->select($callback);
    }

    /**
     * Get statistics on a user's collection.
     *
     * @param int $userID User ID
     *
     * @return array
     */
    public function getUserStatistics($userID)
    {
        $callback = function ($select) use ($userID) {
            $count = new Expression(
                'count(?)',
                ['Item_ID'],
                [Expression::TYPE_IDENTIFIER]
            );
            $select->columns(['Collection_Status', 'Count' => $count]);
            $select->group('Collection_Status');
            $select->where->equalTo('User_ID', $userID);
        };
        $result = $this->select($callback);
        $retVal = ['have' => 0, 'want' => 0, 'extra' => 0];
        foreach ($result as $current) {
            $retVal[$current['Collection_Status']] = $current['Count'];
        }
        return $retVal;
    }
}
