<?php

/**
 * MageFlow
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageflow.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * If you wish to use the MageFlow Connect extension as part of a paid
 * service please contact licence@mageflow.com for information about
 * obtaining an appropriate licence.
 */

/**
 * V1.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_Help_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Help_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Cms
{
    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Help_Rest_Admin_V1
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * retrieve
     *
     * @return array
     */
    public function _retrieve()
    {
        $config = $this->getConfig();
        $out = array();
        $out['info']
            = 'NB! Please mind the original Magento resource
            names as these may not be correct in this listing';
        foreach ($config->getResources() as $name => $resource) {
            $attributes = array();
            foreach (
                $config->getResourceAttributes($name) as $attributeNode =>
                $attributeText
            ) {
                $attributes[] = $attributeNode;
            }
            $routesArr = array();
            $routes = $this->getConfig()->getNode(
                'resources/' . $name . '/routes'
            );
            foreach ($routes->children() as $name => $route) {
                $item = array(
                    'name'  => $name,
                    'route' => (string)$route->route
                );
                $routesArr[] = $item;
            }
            $resourceArr = array(
                'resource_type' => $name,
                'routes'        => $routesArr,
                'attributes'    => $attributes
            );
            $out['resources'][] = $resourceArr;
        }
        return $out;
    }

}
