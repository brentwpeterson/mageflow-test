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
 * Mageflow_Connect_Model_Api2_Cms_Block_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_Cms_Block_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Cms
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'cms_block';

    /**
     * Class constructor
     *
     * @return \Mageflow_Connect_Model_Api2_Cms_Block_Rest_Admin_V1
     */
    public function __construct()
    {
        return parent::__construct();
    }


    /**
     * retrieve
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _retrieve(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(sprintf('%s', $filteredData));

        $out = array('error' => 'implement body');
        return $out;
    }

    /**
     * update
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
     * Handles create (POST) request for cms/block
     *
     * @param array $filteredData
     *
     * @return array|string
     */
    public function _create(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($filteredData, true))
        );

        //we shouldn't have any original data in case of creation
        $originalData = null;
        $handlerReturnArray = Mage::helper('mageflow_connect/handler_cms_block')
            ->handle($filteredData);

        if (is_null($handlerReturnArray)) {
            $this->_error("Could not save CMS block.", 10);
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
        $this->_successMessage("Successfully created new CMS block", 0, $out);
        Mage::helper('mageflow_connect/log')->log(
            sprintf('%s', print_r($out, true))
        );
        return $out;
    }

    /**
     * delete entities
     *
     * @param array $filteredData
     *
     * @return array
     */
    public function _multiDelete(array $filteredData)
    {
        Mage::helper('mageflow_connect/log')->log($filteredData);

        $blockEntity = Mage::getModel('cms/block')
            ->load($filteredData['mf_guid'], 'mf_guid');

        $originalData = $blockEntity->getData();
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
            $blockEntity->delete();
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
