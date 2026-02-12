<?php

/**
 * Test class to validate the Doctrine schema.
 *
 * PHP version 8
 *
 * Copyright (C) Villanova University 2025.
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
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:testing:unit_tests Wiki
 */

namespace GeebyDeebyTest\Db;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaValidator;

/**
 * Test class to validate the Doctrine schema.
 *
 * Class must be final due to use of "new static()" by LiveDatabaseTrait.
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:testing:unit_tests Wiki
 */
final class DoctrineSchemaValidationTest extends \PHPUnit\Framework\TestCase
{
    use \GeebyDeebyTest\Feature\LiveDetectionTrait;
    use \GeebyDeebyTest\Feature\ServiceLocatorTrait;

    /**
     * Standard setup method.
     *
     * @return void
     */
    public function setUp(): void
    {
        // Give up if we're not running in CI:
        if (!$this->continuousIntegrationRunning()) {
            $this->markTestSkipped('Continuous integration not running.');
            return;
        }
    }

    /**
     * Test schema validation.
     *
     * @return void
     */
    public function testSchemaValidation(): void
    {
        $container = $this->getServiceLocator();
        $entityManager = $container->get(EntityManager::class);
        if ($cache = $entityManager->getCache()) {
            // Flush the Doctrine cache to be sure we're validating the latest data:
            $cache->flushAll();
        }
        $validator = new SchemaValidator($entityManager);
        $errorList = $validator->validateMapping();
        $schemaList = $validator->getUpdateSchemaList();
        $this->assertSame(
            [],
            $errorList,
            'Unexpected validation error'
            . (($firstError = reset($errorList)) ? "; first error: $firstError" : '')
        );
        $this->assertSame(
            [],
            $schemaList,
            'Unexpected schema updates pending'
            . (($firstUpdate = reset($schemaList)) ? "; first update: $firstUpdate" : '')
        );
    }
}
