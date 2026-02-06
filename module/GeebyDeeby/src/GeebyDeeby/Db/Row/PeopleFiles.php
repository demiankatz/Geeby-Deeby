<?php

/**
 * Row Definition for People_Files
 *
 * PHP version 8
 *
 * Copyright (C) Demian Katz 2026.
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use GeebyDeeby\Db\Entity\FileEntityInterface;
use GeebyDeeby\Db\Entity\PeopleFileEntityInterface;
use GeebyDeeby\Db\Entity\PersonEntityInterface;

/**
 * Row Definition for People_Files
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class PeopleFiles extends TableAwareGateway implements PeopleFileEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct(['Person_ID', 'File_ID'], 'People_Files', $adapter);
    }

    /**
     * Get associated person.
     *
     * @return PersonEntityInterface
     */
    public function getPerson(): PersonEntityInterface
    {
        return $this->getTableManager()->get('person')->getByPrimaryKey($this->Person_ID);
    }

    /**
     * Set associated person.
     *
     * @param int|PersonEntityInterface $person Associated person entity or ID
     *
     * @return static
     */
    public function setPerson(int|PersonEntityInterface $person): static
    {
        $this->Person_ID = $person instanceof PersonEntityInterface ? $person->getId() : $person;
        return $this;
    }

    /**
     * Get associated file.
     *
     * @return FileEntityInterface
     */
    public function getFile(): FileEntityInterface
    {
        return $this->getTableManager()->get('file')->getByPrimaryKey($this->File_ID);
    }

    /**
     * Set associated file.
     *
     * @param int|FileEntityInterface $file Associated file entity or ID
     *
     * @return static
     */
    public function setFile(int|FileEntityInterface $file): static
    {
        $this->File_ID = $file instanceof FileEntityInterface ? $file->getId() : $file;
        return $this;
    }
}
