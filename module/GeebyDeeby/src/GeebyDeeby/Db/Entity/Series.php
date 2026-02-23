<?php

/**
 * Series entity model.
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
 * Series entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Series')]
#[ORM\Index(name: 'language_id', columns: ['Language_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class Series extends AbstractEntity implements SeriesEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Unique ID.
     *
     * @var int
     */
    #[ORM\Column(name: 'Series_ID', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    /**
     * Series name.
     *
     * @var string
     */
    #[ORM\Column(name: 'Series_Name', type: 'text', length: 255, nullable: false)]
    protected string $seriesName;

    /**
     * Series description.
     *
     * @var string
     */
    #[ORM\Column(name: 'Series_Description', type: 'text', length: 65535, nullable: true)]
    protected string $description;

    /**
     * Language.
     *
     * @var Language
     */
    #[ORM\JoinColumn(
        name: 'Language_ID',
        referencedColumnName: 'Language_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Language::class)]
    protected Language $language;

    /**
     * Get identifier (returns null for an uninitialized or non-persisted object).
     *
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the series.
     *
     * @return string
     */
    public function getSeriesName(): string
    {
        return $this->seriesName;
    }

    /**
     * Set the name of the series.
     *
     * @param string $name New name.
     *
     * @return static
     */
    public function setSeriesName(string $name): static
    {
        $this->seriesName = $name;
        return $this;
    }

    /**
     * Get a description of the series.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the series.
     *
     * @param ?string $description New description
     *
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get associated language.
     *
     * @return LanguageEntityInterface
     */
    public function getLanguage(): LanguageEntityInterface
    {
        return $this->language;
    }

    /**
     * Set associated language.
     *
     * @param int|LanguageEntityInterface $language Associated language entity or ID
     *
     * @return static
     */
    public function setLanguage(int|LanguageEntityInterface $language): static
    {
        if ($language instanceof Language) {
            $this->language = $language;
            return $this;
        } elseif ($language instanceof LanguageEntityInterface) {
            $language = $language->getId();
        }
        $this->language = $this->entityManager->getReference(Language::class, $language);
        return $this;
    }

    /**
     * Get the display name to represent the row to a user.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->getSeriesName();
    }
}
