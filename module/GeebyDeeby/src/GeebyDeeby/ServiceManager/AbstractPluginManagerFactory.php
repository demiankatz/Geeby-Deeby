<?php
/**
 * Abstract plugin manager factory.
 *
 * PHP version 5
 *
 * Copyright (C) Demian Katz 2019.
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category GeebyDeeby
 * @package  ServiceManager
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeeby\ServiceManager;

use Interop\Container\ContainerInterface;

/**
 * Abstract plugin manager factory.
 *
 * @category GeebyDeeby
 * @package  ServiceManager
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AbstractPluginManagerFactory
    implements \Laminas\ServiceManager\Factory\FactoryInterface
{
    /**
     * Determine the configuration key for the specified class name.
     *
     * @param string $requestedName Service being created
     *
     * @return string
     */
    public function getConfigKey($requestedName)
    {
        // Extract namespace of plugin manager (chop off leading top-level
        // namespace -- e.g. GeebyDeeby -- and trailing PluginManager class).
        $regex = '/^[^\\\\]+\\\\(.*)\\\\PluginManager$/';
        preg_match($regex, $requestedName, $matches);
        return strtolower(str_replace('\\', '_', $matches[1]));
    }

    /**
     * Create service
     *
     * @param ContainerInterface $container Service manager
     * @param string             $name      Requested service name
     * @param array              $options   Extra options
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $name,
        array $options = null
    ) {
        if (!empty($options)) {
            throw new \Exception('Unexpected options sent to factory.');
        }
        $configKey = $this->getConfigKey($name);
        if (empty($configKey)) {
            $error = 'Problem determining config key for ' . $name;
            throw new \Exception($error);
        }
        $config = $container->get('Config');
        return new $name(
            $container,
            $config['geeby-deeby']['plugin_managers'][$configKey] ?? []
        );
    }
}
