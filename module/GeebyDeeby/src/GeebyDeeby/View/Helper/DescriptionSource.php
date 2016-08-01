<?php
/**
 * Description source name view helper
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
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\View\Helper;

/**
 * Description source name view helper
 *
 * @category GeebyDeeby
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class DescriptionSource extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Descriptions of source types
     *
     * @var array
     */
    protected $descriptionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->descriptionTypes = array(
            'User' => 'User Summary',
            'LC' => 'LC Cataloging in Publication Summary',
            'Cover' => 'Cover Text',
            'Ad' => 'Advertisement Blurb',
        );
    }

    /**
     * Get the full list of description type information
     *
     * @return array
     */
    public function getList()
    {
        return $this->descriptionTypes;
    }

    /**
     * Convert a raw source type into a description
     *
     * @param string $source Source type (from Items_Descriptions table)
     *
     * @return string
     */
    public function getName($source)
    {
        return isset($this->descriptionTypes[$source])
            ? $this->descriptionTypes[$source] : 'Unknown';
    }
}
