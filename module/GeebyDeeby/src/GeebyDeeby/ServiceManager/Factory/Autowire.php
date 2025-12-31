<?php

/**
 * Autowiring Attribute (adapted from VuFind)
 *
 * PHP version 8
 *
 * Copyright (C) The National Library of Finland 2025.
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
 * @package  ServiceManager
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */

namespace GeebyDeeby\ServiceManager\Factory;

use Attribute;

/**
 * Autowiring Attribute (adapted from VuFind)
 *
 * @category GeebyDeeby
 * @package  ServiceManager
 * @author   Ere Maijala <ere.maijala@helsinki.fi>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
#[Attribute]
class Autowire
{
    /**
     * Constructor.
     *
     * @param ?string $service   Service to inject (mutually exclusive with $config)
     * @param ?string $container Container or plugin manager to use to get the service
     */
    public function __construct(
        public readonly ?string $service = null,
        public readonly ?string $container = null,
    ) {
    }
}
