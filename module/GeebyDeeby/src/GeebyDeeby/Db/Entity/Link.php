<?php

/**
 * Link entity model.
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
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */

namespace GeebyDeeby\Db\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Links')]
#[ORM\Index(name: 'link_type_id', columns: ['Link_Type_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Link extends AbstractEntity implements LinkEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Link_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Link name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Link_Name', type: 'text', length: 255, nullable: false)]
    protected string $linkName;

    /**
     * URL.
     *
     * @var string
     */
    #[ORM\Column(name: 'URL', type: 'text', length: 255, nullable: false)]
    protected string $url;

    /**
     * Description.
     *
     * @var string
     */
    #[ORM\Column(name: 'Description', type: 'text', length: 255, nullable: true)]
    protected string $description;

    /**
     * Date checked.
     *
     * @var ?DateTime
     */
    #[ORM\Column(name: 'Date_Checked', type: 'date', nullable: false, options: ['default' => '0000-00-00'])]
    protected ?DateTime $checked;

    /**
     * Link type.
     *
     * @var LinkType
     */
    #[ORM\JoinColumn(
        name: 'Link_Type_ID',
        referencedColumnName: 'Link_Type_ID',
        nullable: false,
        options: ['default' => 0],
    )]
    #[ORM\ManyToOne(targetEntity: LinkType::class)]
    protected LinkType $linkType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->checked = new DateTime();
    }

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    /**
     * Get the name of the link.
     *
     * @return string
     */
    public function getLinkName(): string
    {
        return $this->linkName;
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
        $this->linkName = $name;
        return $this;
    }

    /**
     * Get the URL of the link.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
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
        $this->url = $url;
        return $this;
    }

    /**
     * Get a description of the link.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of the link.
     *
     * @param string $description New description
     *
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set the date the link was last checked.
     *
     * @return DateTime
     */
    public function getDateChecked(): DateTime
    {
        return $this->checked;
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
        $this->checked = $checked instanceof DateTime ? $checked : DateTime::createFromFormat('Y-m-d', $checked);
        return $this;
    }

    /**
     * Get associated link type.
     *
     * @return LinkTypeEntityInterface
     */
    public function getLinkType(): LinkTypeEntityInterface
    {
        return $this->linkType;
    }

    /**
     * Set associated link type.
     *
     * @param int|LinkTypeEntityInterface $linkType Associated linkType entity or ID
     *
     * @return static
     */
    public function setLinkType(int|LinkTypeEntityInterface $linkType): static
    {
        if ($linkType instanceof LinkType) {
            $this->linkType = $linkType;
            return $this;
        } elseif ($linkType instanceof LinkTypeEntityInterface) {
            $linkType = $linkType->getId();
        }
        $this->linkType = $this->entityManager->getReference(LinkType::class, $linkType);
        return $this;
    }
}
