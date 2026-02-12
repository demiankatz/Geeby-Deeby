<?php

/**
 * Class to load information into the database.
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
 * @package  Ingest
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeebyLocal\Ingest;

use GeebyDeeby\Db\Service\DbServiceInterface;

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
    public const FULLTEXT_SOURCE_VU = 1;
    public const FULLTEXT_SOURCE_IA = 3;
    public const FULLTEXT_SOURCE_NIU = 10;
    public const FULLTEXT_SOURCE_USF = 11;
    public const FULLTEXT_SOURCE_STANFORD = 13;
    public const FULLTEXT_SOURCE_BGSU = 14;
    public const MATERIALTYPE_WORK = 1;
    public const MATERIALTYPE_ISSUE = 2;
    public const PREDICATE_OWL_SAMEAS = 2;
    public const ROLE_AUTHOR = 1;
    public const ROLE_EDITOR = 4;
    public const ROLE_ILLUSTRATOR = 2;
    public const ROLE_TRANSLATOR = 3;
    public const TAGTYPE_LC = 1;

    /**
     * Constructor
     *
     * @param \GeebyDeeby\Db\Service\PluginManager $services Database service manager
     */
    public function __construct(protected \GeebyDeeby\Db\Service\PluginManager $services)
    {
    }

    /**
     * Get a database service.
     *
     * @param class-string<T> $name Name of service to retrieve
     *
     * @template T
     *
     * @return T
     */
    protected function getDbService(string $name): DbServiceInterface
    {
        return $this->services->get($name);
    }
}
