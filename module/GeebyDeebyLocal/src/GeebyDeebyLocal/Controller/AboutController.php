<?php
/**
 * About controller
 *
 * PHP version 5
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
namespace GeebyDeebyLocal\Controller;
use Zend\Cache\StorageFactory;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

/**
 * About controller
 *
 * @category GeebyDeeby
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://github.com/demiankatz/Geeby-Deeby Main Site
 */
class AboutController extends \GeebyDeeby\Controller\AbstractBase
{
    /**
     * Credits page
     *
     * @return mixed
     */
    public function creditsAction()
    {
        return $this->createViewModel();
    }

    /**
     * About page
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->createViewModel();
    }

    /**
     * Progress page
     *
     * @return mixed
     */
    public function progressAction()
    {
        $dir = __DIR__ . '/../../../../../data/cache';
        $opts = array('cache_dir' => $dir, 'ttl' => 60 * 60 * 24);
        $settings = array(
            'adapter' => array('name' => 'filesystem', 'options' => $opts),
            'plugins' => array('serializer')
        );
        $cache = StorageFactory::factory($settings);
        if (!($stats = $cache->getItem('progressStats'))) {
            $stats = $this->getProgressStatistics();
            $cache->setItem('progressStats', $stats);
        }
        return $this->createViewModel(array('progress' => $stats));
    }

    /**
     * Retrieve progress statistics.
     *
     * @return array
     */
    protected function getProgressStatistics()
    {
        $s = $this->getDbTable('series');
        $callback = function ($select) {
            $select->columns(
                array(
                    'Series_ID' => 'Series_ID',
                    'Series_Name' => 'Series_Name',
                    'Item_Count' => new Expression(
                        'count(distinct(?))', array('Item_ID'),
                        array(Expression::TYPE_IDENTIFIER)
                    ),
                    'Complete' => new Expression(
                        'if (Series_Description like \'%in progress%\', 0, 1)'
                    ),
                )
            );
            $select->join(
                array('e' => 'Editions'),
                'Series.Series_ID = e.Series_ID', array(), Select::JOIN_LEFT
            );
            $select->group(array('Series.Series_ID'));
            $select->order(array('Series.Series_Name'));
        };
        return $s->select($callback)->toArray();
    }
}
