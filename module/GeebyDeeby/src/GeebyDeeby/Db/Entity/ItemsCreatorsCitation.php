<?php

/**
 * Item creator/citation link entity model.
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
 * Item creator/citation link entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Items_Creators_Citations')]
#[ORM\Index(name: 'idx_c22bec3fb5e11f88', columns: ['Item_Creator_ID'])]
#[ORM\Index(name: 'citation_id', columns: ['Citation_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class ItemsCreatorsCitation extends AbstractEntity implements ItemsCreatorsCitationEntityInterface
{
    use Feature\EntityManagerAwareTrait;

    /**
     * Item creator.
     *
     * @var ItemsCreator
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Item_Creator_ID',
        referencedColumnName: 'Item_Creator_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: ItemsCreator::class)]
    protected ItemsCreator $creator;

    /**
     * Citation.
     *
     * @var Citation
     */
    #[ORM\Id]
    #[ORM\JoinColumn(
        name: 'Citation_ID',
        referencedColumnName: 'Citation_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\ManyToOne(targetEntity: Citation::class)]
    protected Citation $citation;

    /**
     * Get associated creator.
     *
     * @return ItemsCreatorEntityInterface
     */
    public function getCreator(): ItemsCreatorEntityInterface
    {
        return $this->creator;
    }

    /**
     * Set associated creator.
     *
     * @param int|ItemsCreatorEntityInterface $creator Associated creator entity or ID
     *
     * @return static
     */
    public function setCreator(int|ItemsCreatorEntityInterface $creator): static
    {
        if ($creator instanceof ItemsCreator) {
            $this->creator = $creator;
            return $this;
        } elseif ($creator instanceof ItemsCreatorEntityInterface) {
            $creator = $creator->getId();
        }
        $this->creator = $this->entityManager->getReference(ItemsCreator::class, $creator);
        return $this;
    }

    /**
     * Get associated citation.
     *
     * @return CitationEntityInterface
     */
    public function getCitation(): CitationEntityInterface
    {
        return $this->citation;
    }

    /**
     * Set associated citation.
     *
     * @param int|CitationEntityInterface $citation Associated citation entity or ID
     *
     * @return static
     */
    public function setCitation(int|CitationEntityInterface $citation): static
    {
        if ($citation instanceof Citation) {
            $this->citation = $citation;
            return $this;
        } elseif ($citation instanceof CitationEntityInterface) {
            $citation = $citation->getId();
        }
        $this->citation = $this->entityManager->getReference(Citation::class, $citation);
        return $this;
    }
}
