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
 * Mageflow_Connect_Model_Api2_System_Maintenance_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Maintenance_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_website';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_System_Maintenance_Rest_Admin_V1
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array();
        $out['resource_type'] = $this->_resourceType;
        $this->log($this->getRequest()->getParams());
        $mode = $this->getRequest()->getParam('mode');
        $out['items']['enabled'] = Mage::app()->getStore()->getConfig(
            'mageflow_connect/system/maintenance_mode'
        );
        $out['items']['ip_list'] = array();
        $allowIps = Mage::app()->getStore()->getConfig(
            'dev/restrict/allow_ips'
        );
        if (!is_null($allowIps)) {
            $out['items']['ip_list'] = array_map(
                'trim',
                explode(',', $allowIps)
            );
        }
        $this->log($out);

        return $this->prepareResponse($out);
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }

    /**
     * update
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _update(array $filteredData)
    {
        $this->log(__METHOD__);
        $this->log($filteredData);
        if (isset($filteredData['items']['enabled'])) {
            $this->log(
                'setting maintenance mode to '
                . $filteredData['items']['enabled']
            );
            Mage::app()->getConfig()->saveConfig(
                'mageflow/system/maintenance_mode',
                (int)$filteredData['items']['enabled']
            );
        }
        if (isset($filteredData['items']['ip_list'])) {
            Mage::app()->getConfig()->saveConfig(
                'dev/restrict/allow_ips',
                implode(',', $filteredData['items']['ip_list'])
            );
        }

        $this->getResponse()->addMessage(
            'status',
            self::STATUS_SUCCESS,
            array(),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );

        if (isset($filteredData['items']['clean_cache'])
            && $filteredData['items']['clean_cache']
        ) {
            $this->log('cleaning cache');
            Mage::app()->cleanCache();
        }
        return $filteredData;
    }

    /**
     * multi update
     *
     * @param array $filteredData
     */
    public function _multiUpdate(array $filteredData)
    {
        $this->log(__METHOD__);
        foreach ($filteredData as $data) {
            $this->_update($data);
        }
    }

    /**
     * create
     *
     * @param array $filteredData
     */
    public function _create(array $filteredData)
    {
        $this->log(__METHOD__);
        $this->log($filteredData);
        $this->getResponse()->addMessage(
            'status',
            self::STATUS_SUCCESS,
            array(),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );
    }

    /**
     * multi create
     *
     * @param array $filteredData
     */
    public function _multiCreate(array $filteredData)
    {
        $this->log(__METHOD__);
        foreach ($filteredData as $data) {
            $this->_create($data);
        }
    }

}