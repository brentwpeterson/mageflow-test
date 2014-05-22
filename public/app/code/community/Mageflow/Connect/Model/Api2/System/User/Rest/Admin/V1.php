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
 * Mageflow_Connect_Model_Api2_System_User_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_User_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_user';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_System_User_Rest_Admin_V1
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

        $this->log($this->getRequest()->getParams());
        $model = $this->getWorkingModel();
        $itemCollection = $model->getCollection();
        if ($this->getRequest()->getParam('id', 0) > 0) {
            $id = $this->getRequest()->getParam('id');
            $itemCollection->addFieldToFilter('user_id', $id);
        }
        $items = $itemCollection->toArray();
        $this->log($items);
        for ($i = 0; $i < sizeof($items['items']); $i++) {
            unset($items['items'][$i]['password']);
        }
        return $items['items'];
    }

    /**
     * retreive collection
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
     */
    public function _update(array $filteredData)
    {
        $this->log(__METHOD__);
        $this->log($filteredData);
        $model = $this->getWorkingModel();
        $model->load($filteredData['id']);
        unset($filteredData['id']);
        $model->setData('is_active', $filteredData['is_active']);
        $model->save();
        $this->getResponse()->addMessage(
            'status',
            self::STATUS_SUCCESS,
            array(),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );
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
     *
     * @return mixed
     */
    public function _create(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );
        $originalCacheSettings = Mage::helper('mageflow_connect/system')
            ->cacheSettings();
        Mage::helper('mageflow_connect/system')->cacheSettings(
            array('all' => 0)
        );

        //we shouldn't have any original data in case of creation
        $originalData = null;
        $handlerReturnArray = Mage::helper(
            'mageflow_connect/handler_system_user'
        )
            ->handle($filteredData);

        if (is_null($handlerReturnArray)) {
            $this->_error("Could not save User.", 10);
        }

        $entity = $handlerReturnArray['entity'];
        $originalData = $handlerReturnArray['original_data'];

        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if (!is_null($originalData)) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        }
        $out = $entity->getData();
        $this->_successMessage("Successfully saved User", 0, $out);
        Mage::helper('mageflow_connect/system')->cacheSettings(
            $originalCacheSettings
        );
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($out, true))
        );
        return $out;
    }
}