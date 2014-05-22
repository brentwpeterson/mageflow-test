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
 * PullController.php
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
 * Mageflow_Connect_PullController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_PullController
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
                'mageflow_connect/adminhtml_pull',
                'mageflow_connect.pullgrid'
            )
        );

        $this->renderLayout();
    }

    /**
     * Apply changeset
     */
    public function pullAction()
    {
        $params = $this->getRequest()->getParams();
        $this->getLogger()->log($params, __METHOD__, __LINE__);

        $company = Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_COMPANY
        );
        $data = array(
            'company' => $company,
        );

        $client = $this->getApiClient();

        $idList = $this->getRequest()->getParam('id', array());
        $idArr = array();
        if (is_scalar($idList)) {
            $idArr[] = $idList;
        } else {
            $idArr = $idList;
        }
        $this->getLogger()->log($idArr, __METHOD__, __LINE__);
        foreach ($idArr as $id) {
            $data['id'] = $id;
            $response = $client->get('changesetitem', $data);

            $itemArray = json_decode($response, true);
            $item = $itemArray['items'][0];
            $this->getLogger()->log($item, __METHOD__, __LINE__);
            $filteredData = json_decode($item['content'], true);
            $this->getLogger()->log($filteredData, __METHOD__, __LINE__);

            switch ($item['type']) {
                case "cms/block" :
                    Mage::helper('mageflow_connect/handler_cms_block')->handle(
                        $filteredData
                    );
                    break;
                case "cms/page" :
                    Mage::helper('mageflow_connect/handler_cms_page')->handle(
                        $filteredData
                    );
                    break;
                case "catalog/category" :
                    Mage::helper('mageflow_connect/handler_catalog_category')
                        ->handle($filteredData);
                    break;
                case "catalog/resource_eav_attribute" :
                    Mage::helper('mageflow_connect/handler_catalog_attribute')
                        ->handle($filteredData);
                    break;
                case "system/configuration" :
                    Mage::helper(
                        'mageflow_connect/handler_system_configuration'
                    )->handle($filteredData);
                    break;
                case "eav/entity_attribute_set" :
                    Mage::helper(
                        'mageflow_connect/handler_catalog_attributeset'
                    )->handle($filteredData);
                    break;
            }
        }
        $this->_redirect('*/pull/index');
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $contentBlock = $this->getLayout()->createBlock(
            'mageflow_connect/adminhtml_pull_grid'
        );
        $html = $contentBlock->toHtml();
        $this->getResponse()->setBody($html);
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
        $this->_redirect('*/pull/index');
    }

}
