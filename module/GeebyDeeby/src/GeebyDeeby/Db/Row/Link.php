<?php

/**
 * Row Definition for Links
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
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\Db\Row;

use DateTime;
use GeebyDeeby\Db\Entity\LinkEntityInterface;
use GeebyDeeby\Db\Entity\LinkTypeEntityInterface;

/**
 * Row Definition for Links
 *
 * @category GeebyDeeby
 * @package  Db_Row
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class Link extends TableAwareGateway implements LinkEntityInterface
{
    /**
     * Constructor
     *
     * @param \Laminas\Db\Adapter\Adapter $adapter Database adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('Link_ID', 'Links', $adapter);
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->Link_ID ?? null;
    }

    /**
     * Get the name of the link.
     *
     * @return string
     */
    public function getLinkName(): string
    {
        return $this->Link_Name;
    }

    /**
     * Set the name of the link.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setLinkName(string $name): static
    {
        $this->Link_Name = $name;
        return $this;
    }

    /**
     * Get the URL of the link.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->URL;
    }

    /**
     * Set the URL of the link.
     *
     * @param string $url New url.
     *
     * @return static
     */
    public function setUrl(string $url): static
    {
        $this->URL = $url;
        return $this;
    }

    /**
     * Get a description of the link.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->Description;
    }

    /**
     * Set the link.
     *
     * @param string $description New description
     *
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->Description = $description;
        return $this;
    }

    /**
     * Get the date the link was last checked.
     *
     * @return DateTime
     */
    public function getDateChecked(): DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $this->Date_Checked);
    }

    /**
     * Set the date the link was last checked.
     *
     * @param string|DateTime $checked Checked date
     *
     * @return static
     */
    public function setDateChecked(string|DateTime $checked): static
    {
        $this->Date_Checked = $checked instanceof DateTime ? $checked->format('Y-m-d') : $checked;
        return $this;
    }

    /**
     * Get associated link type.
     *
     * @return LinkTypeEntityInterface
     */
    public function getLinkType(): LinkTypeEntityInterface
    {
        return $this->getTableManager()->get('linktype')->getByPrimaryKey($this->Link_Type_ID);
    }

    /**
     * Set associated link type.
     *
     * @param int|LinkTypeEntityInterface $linkType Associated language entity or ID
     *
     * @return static
     */
    public function setLinkType(int|LinkTypeEntityInterface $linkType): static
    {
        $this->Link_Type_ID = $linkType instanceof LinkTypeEntityInterface
            ? $linkType->getId() : $linkType;
        return $this;
    }
}
