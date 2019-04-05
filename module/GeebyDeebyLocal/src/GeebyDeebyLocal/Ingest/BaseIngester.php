<?php
/**
 * Class to load information into the database.
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
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Ingest;

/**
 * Class to load information into the database.
 *
 * @category GeebyDeeby
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
abstract class BaseIngester
{
    // constant values drawn from dimenovels.org database:
    const FULLTEXT_SOURCE_VU = 1;
    const FULLTEXT_SOURCE_IA = 3;
    const FULLTEXT_SOURCE_NIU = 10;
    const FULLTEXT_SOURCE_BGSU = 14;
    const MATERIALTYPE_WORK = 1;
    const MATERIALTYPE_ISSUE = 2;
    const PREDICATE_OWL_SAMEAS = 2;
    const ROLE_AUTHOR = 1;
    const TAGTYPE_LC = 1;

    /**
     * Table plugin manager
     *
     * @var object
     */
    protected $tables;

    /**
     * Constructor
     *
     * @param object $tables Table plugin manager
     */
    public function __construct($tables)
    {
        $this->tables = $tables;
    }

    /**
     * Get a database table gateway.
     *
     * @param string $table Name of table service to pull
     *
     * @return \Zend\Db\TableGateway\AbstractTableGateway
     */
    protected function getDbTable($table)
    {
        return $this->tables->get($table);
    }
}
