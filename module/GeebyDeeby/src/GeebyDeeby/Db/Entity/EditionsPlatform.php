<?php

/**
 * Edition platform entity model.
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
 * Edition platform entity model.
 *
 * @category GeebyDeeby
 * @package  Database
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:plugins:database_gateways Wiki
 */
#[ORM\Table(name: 'Editions_Platforms')]
#[ORM\Index(name: 'idx_c756ab50959f1de4', columns: ['Edition_ID'])]
#[ORM\Index(name: 'platform_id', columns: ['Platform_ID'])]
#[ORM\Entity]
#[ORM\EntityListeners([Feature\EntityManagerInjector::class])]
class EditionsPlatform extends AbstractEntity implements EditionsPlatformEntityInterface
{
    use Feature\EntityManagerAwareTrait;

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
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Edition::class)]
    protected Edition $edition;

    /**
     * Platform.
     *
     * @var Platform
     */
    #[ORM\JoinColumn(
        name: 'Platform_ID',
        referencedColumnName: 'Platform_ID',
        nullable: false,
        options: ['default' => 0]
    )]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Platform::class)]
    protected Platform $platform;

    /**
     * Get associated edition.
     *
     * @return EditionEntityInterface
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
    public function setEdition(int|EditionEntityInterface $edition): static
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
     * Get associated platform.
     *
     * @return PlatformEntityInterface
     */
    public function getPlatform(): PlatformEntityInterface
    {
        return $this->platform;
    }

    /**
     * Set associated platform.
     *
     * @param int|PlatformEntityInterface $platform Associated platform entity or ID
     *
     * @return static
     */
    public function setPlatform(int|PlatformEntityInterface $platform): static
    {
        if ($platform instanceof Platform) {
            $this->platform = $platform;
            return $this;
        } elseif ($platform instanceof PlatformEntityInterface) {
            $platform = $platform->getId();
        }
        $this->platform = $this->entityManager->getReference(Platform::class, $platform);
        return $this;
    }
}
