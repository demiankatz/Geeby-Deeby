<?php

/**
 * Edition full text entity model.
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

use Doctrine\ORM\Mapping as ORM;

/**
 * Edition full text entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Full_Text')]
#[ORM\Index(name: 'full_text_source_id', columns: ['Full_Text_Source_ID'])]
#[ORM\Index(name: 'edition_id', columns: ['Edition_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsFullText extends AbstractEntity implements EditionsFullTextEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Sequence_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Full text source.
     *
     * @var FullTextSource
     */
    #[ORM\JoinColumn(
        name: 'Full_Text_Source_ID',
        referencedColumnName: 'Full_Text_Source_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: FullTextSource::class)]
    protected FullTextSource $source;

    /**
     * Edition.
     *
     * @var Edition
     */
    #[ORM\JoinColumn(
        name: 'Edition_ID',
        referencedColumnName: 'Edition_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * URL.
     *
     * @var string
     */
    #[ORM\Column(name: 'Full_Text_URL', type: 'text', length: 255, nullable: false)]
    protected string $url;

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
     * Get associated edition.
     *
     * @return ?EditionEntityInterface
     */
    public function getEdition(): EditionEntityInterface
    {
        return $this->edition;
    }

    /**
     * Set associated edition.
     *
     * @param int|EditionEntityInterface $edition Associated edition entity or ID
     *
     * @return static
     */
    public function setEdition(int|EditionEntityInterface|null $edition): static
    {
        if ($edition instanceof Edition) {
            $this->edition = $edition;
            return $this;
        } elseif ($edition instanceof EditionEntityInterface) {
            $edition = $edition->getId();
        }
        $this->edition = $this->entityManager->getReference(Edition::class, $edition);
        return $this;
    }

    /**
     * Get associated full text source id.
     *
     * @return FullTextSourceEntityInterface
     */
    public function getFullTextSource(): FullTextSourceEntityInterface
    {
        return $this->source;
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
        if ($fts instanceof FullTextSource) {
            $this->source = $fts;
            return $this;
        } elseif ($fts instanceof FullTextSourceEntityInterface) {
            $fts = $fts->getId();
        }
        $this->source = $this->entityManager->getReference(FullTextSource::class, $fts);
        return $this;
    }

    /**
     * Get the URL of the full text resource.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
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
        $this->url = $url;
        return $this;
    }
}
