<?php

/**
 * PushController.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_PushController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_PushController
    extends Mageflow_Connect_Controller_AbstractController
{
    /**
     * Index action
     */
    public function indexAction()
    {

        $this->loadLayout();
        $this->_setActiveMenu('mageflow/connect');
        $this->_addContent(
            $this->getLayout()->createBlock(
                'mageflow_connect/adminhtml_push',
                'mageflow_connect.pushgrid'
            )
        );
        $this->renderLayout();
    }

    /**
     * Pushes changesets to MageFlow
     */
    public function pushAction()
    {
        $params = $this->getRequest()->getParams();
        $this->getLogger()->log($params, __METHOD__, __LINE__);


        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        $changesetItemList = Mage::getModel('mageflow_connect/changeset_item')
            ->getCollection()
            ->addFieldToFilter(
                'id',
                array('in' => $idArr
                )
            );

        /**
         * TODO
         *
         * add changeset items to changeset
         * get client
         * client-> send changeset
         */
        $itemData = array();
        foreach ($changesetItemList as $changesetItem) {
//            $this->getLogger()->log($changesetItem->getId());

            $dataItem = array(
                'type'     => str_replace(
                    array('::', ':'),
                    '/',
                    $changesetItem->getType()
                ),
                'content'  => $changesetItem->getContent(),
                'encoding' => $changesetItem->getEncoding(),
            );
            if ($changesetItem->getMetainfo()) {
                $dataItem['metainfo'] = $changesetItem->getMetainfo();
            } else {
                $dataItem['metainfo'] = array();
            }
            $itemData[] = $dataItem;
        }
        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'company'     => $company,
            'instance'    => Mage::app()->getStore()->getConfig(
                \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
            ),
            'description' => $this->getRequest()->getParam('comment'),
            'items'       => json_encode($itemData),
        );

        $client = $this->getApiClient();
        $response = $client->post('changeset', $data);


        foreach ($changesetItemList as $changesetItem) {
            $changesetItem->setStatus(
                Mageflow_Connect_Model_Changeset_Item::STATUS_SENT
            );
            $changesetItem->setUpdatedAt(now());
            $changesetItem->save();
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Apply changeset
     */
    public function applyAction()
    {
        $params = $this->getRequest()->getParams();
        $this->getLogger()->log($params, __METHOD__, __LINE__);

        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        $changesetItemList = Mage::getModel('mageflow_connect/changeset_item')
            ->getCollection()
            ->addFieldToFilter(
                'id',
                array('in' => $idArr
                )
            );
        $this->getLogger()->log(
            count($changesetItemList),
            __METHOD__,
            __LINE__
        );
        /**
         * TODO
         *
         * add changeset items to changeset
         * get client
         * client-> send changeset
         */
        $itemData = array();
        foreach ($changesetItemList as $changesetItem) {
            $filteredData = json_decode($changesetItem->getContent(), true);
            switch ($changesetItem->getType()) {
                case "cms:block" :
                    Mage::helper('mageflow_connect/handler_cms_block')->handle(
                        $filteredData
                    );
                    break;
                case "cms:page" :
                    Mage::helper('mageflow_connect/handler_cms_page')->handle(
                        $filteredData
                    );
                    break;
                case "catalog:category" :
                    Mage::helper('mageflow_connect/handler_catalog_category')
                        ->handle($filteredData);
                    break;
                case "catalog:resource_eav_attribute" :
                    Mage::helper('mageflow_connect/handler_catalog_attribute')
                        ->handle($filteredData);
                    break;
                case "system:configuration" :
                    Mage::helper(
                        'mageflow_connect/handler_system_configuration'
                    )->handle($filteredData);
                    break;
                case "eav:entity_attribute_set" :
                    Mage::helper(
                        'mageflow_connect/handler_catalog_attributeset'
                    )->handle($filteredData);
                    break;
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->getLogger()->log($this->getRequest()->getParams());
        $this->loadLayout();
        $contentBlock = $this->getLayout()->createBlock(
            'mageflow_connect/adminhtml_push_grid'
        );
        $this->getResponse()->setBody(
            $contentBlock->toHtml()
        );
    }

    /**
     * Discards changesets
     */
    public function discardAction()
    {
        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        foreach ($idArr as $id) {
            $changesetItem = Mage::getModel('mageflow_connect/changeset_item')
                ->load($id);
            $changesetItem->delete();
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Flushes all changesets (truncates table)
     */
    public function flushAction()
    {
        /**
         * @var Mageflow_Connect_Model_Resource_Changeset_Item_Collection
         * $collection
         */
        Mage::getResourceModel('mageflow_connect/changeset_item')->truncate();
        $this->_redirect('*/*/index');
    }

    /**
     * Refreshes media index
     */
    public function refreshMediaIndexAction()
    {
        /**
         * @var Mageflow_Connect_Helper_Media $mediaIndexHelper
         */
        $mediaIndexHelper = Mage::helper('mageflow_connect/media');
        $mediaIndexHelper->refreshIndex(true);

        $jsonData = Mage::helper('core')->jsonEncode(array());
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
        return;
    }
}
