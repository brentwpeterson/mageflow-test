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
 * Mageflow_Connect_Model_Api2_Cms_Page_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Page_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Cms
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_page';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Cms_Page_Rest_Admin_V1
     */
    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    /**
     * GET request to retrieve a single CMS page
     *
     * @return array|mixed
     */
    public function _retrieve()
    {
        Mage::log(
            sprintf(
                '%s(%s): %s',
                __METHOD__,
                __LINE__,
                print_r($this->getRequest()->getParams(), true)
            )
        );
        $pageId = (int)Mage::getModel('cms/page')->checkIdentifier(
            $this->getRequest()->getParam('key'),
            $this->getRequest()->getParam('store')
        );

        $out = array();
        if ($pageId) {
            $page = Mage::getModel('cms/page')->load($pageId);
            $out = $page->getData();
        }
        Mage::log(
            sprintf(
                '%s(%s): %s',
                __METHOD__,
                __LINE__,
                print_r($out, true)
            )
        );
        return $out;
    }

    /**
     * PUT request to update a single CMS page
     *
     * @param array $filteredData
     *
     * @return array|string|void
     */
    public function _update(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(sprintf('%s', $filteredData));
        return $this->_create($filteredData);
    }

    /**
     * Handles create (POST) request for cms/page
     *
     * @param array $filteredData
     *
     * @return Mage_Core_Model_Abstract
     */
    public function _create(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );

        //we shouldn't have any original data in case of creation
        $originalData = null;
        $handlerReturnArray = Mage::helper('mageflow_connect/handler_cms_page')
            ->handle($filteredData);

        if (is_null($handlerReturnArray)) {
            $this->_error("Could not save CMS page.", 10);
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
        $this->_successMessage("Successfully created new CMS page", 0, $out);
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($out, true))
        );
        return $out;
    }

    /**
     * DELETE to delete a collection of pages
     *
     * @param array $filteredData
     */
    public function _multiDelete(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log($filteredData);

        $pageEntity = Mage::getModel('cms/page')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $originalData = $pageEntity->getData();
        $rollbackFeedback = array();
        // send overwritten data to mageflow
        if ($originalData) {
            $rollbackFeedback = $this->sendRollback(
                str_replace('_', ':', $this->_resourceType),
                $filteredData,
                $originalData
            );
        } else {
            $this->sendJsonResponse(
                ['notice' => 'target not found or empty, mf_guid='
                . $filteredData['mf_guid']]
            );
        }
        try {
            $pageEntity->delete();
            $this->sendJsonResponse(
                array_merge(
                    ['message' =>
                    'target deleted, mf_guid=' . $filteredData['mf_guid']],
                    $rollbackFeedback
                )
            );
        } catch (Exception $e) {
            $this->sendJsonResponse(
                array_merge(
                    ['delete error' => $e->getMessage()],
                    $rollbackFeedback
                )
            );
        }
    }

}
