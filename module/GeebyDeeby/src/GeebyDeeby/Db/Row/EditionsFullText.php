<?php

/**
 * Row Definition for Editions_Full_Text
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

use GeebyDeeby\Db\Entity\EditionEntityInterface;
use GeebyDeeby\Db\Entity\EditionsFullTextEntityInterface;
use GeebyDeeby\Db\Entity\FullTextSourceEntityInterface;

/**
 * Row Definition for Editions_Full_Text
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class EditionsFullText extends TableAwareGateway implements EditionsFullTextEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Sequence_ID', 'Editions_Full_Text', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Sequence_ID ?? null;
    }

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->getTableManager()->get('edition')->getByPrimaryKey($this->Edition_ID);
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface $edition): static
    {
        $this->Edition_ID = $edition instanceof EditionEntityInterface ? $edition->getId() : $edition;
        return $this;
    }

    /**
     * Get associated full text source id.
     *
     * @return FullTextSourceEntityInterface
     */
    public function getFullTextSource(): FullTextSourceEntityInterface
    {
        return $this->getTableManager()->get('fulltextsource')->getByPrimaryKey($this->Full_Text_Source_ID);
    }

    /**
     * Set associated full text source id.
     *
     * @param int|FullTextSourceEntityInterface $fts Associated full text source entity or ID
     *
     * @return static
     */
    public function setFullTextSource(int|FullTextSourceEntityInterface $fts): static
    {
        $this->Full_Text_Source_ID = $fts instanceof FullTextSourceEntityInterface ? $fts->getId() : $fts;
        return $this;
    }

    /**
     * Get the URL of the full text resource.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->Full_Text_URL;
    }

    /**
     * Set the URL of the full text resource.
     *
     * @param string $url New url.
     *
     * @return static
     */
    public function setUrl(string $url): static
    {
        $this->Full_Text_URL = $url;
        return $this;
    }
}
