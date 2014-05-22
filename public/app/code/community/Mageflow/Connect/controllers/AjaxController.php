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
 * AjaxController.php
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
 * Mageflow_Connect_AjaxController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_AjaxController
    extends Mageflow_Connect_Controller_AbstractController
{
    /**
     * index action
     *
     */
    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
    }

    /**
     * Queries for enabled companies at MageFlow
     * and returns the list. It also returns URL
     * of next AJAX call
     *
     */
    public function getcompaniesAction()
    {
        $this->getLogger()->log(
            $this->getRequest()->getParams(),
            __METHOD__,
            __LINE__
        );
        //save magento configuration fields that are set in the backend
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_ENABLED,
            1
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY,
            trim($this->getRequest()->getParam('consumer_key'))
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET,
            trim($this->getRequest()->getParam('consumer_secret'))
        );
        Mage::app()->getConfig()
            ->saveConfig(
                Mageflow_Connect_Model_System_Config::API_TOKEN,
                trim($this->getRequest()->getParam('token'))
            );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET,
            trim($this->getRequest()->getParam('token_secret'))
        );
        Mage::app()->getConfig()
            ->saveConfig(
                Mageflow_Connect_Model_System_Config::API_URL,
                trim($this->getRequest()->getParam('api_url'))
            );
        if (
            Mage::app()->getStore()
                ->getConfig(Mageflow_Connect_Model_System_Config::API_URL) != ''
        ) {
            $client = $this->getApiClient();
            $response = json_decode($client->get('company', array()));

            $response->project_query_url = Mage::helper('adminhtml')
                    ->getUrl('mageflow_connect/ajax/getprojects')
                . '?isAjax=true';
            $this->getLogger()->log(
                $response->project_query_url,
                __METHOD__,
                __LINE__
            );
        } else {
            $response = new stdClass();
            $response->status = 0;
            $response->error = true;
            $response->statusmessage =
                'API URL not defined. Please set API URL, save configuration'.
                ' and try again.';
        }
        $jsonData = Mage::helper('core')->jsonEncode($response);

        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * Returns list of projects and
     * URL for instance registration
     */
    public function getprojectsAction()
    {
        $this->getLogger()->log(
            $this->getRequest()->getParams(),
            __METHOD__,
            __LINE__
        );
        $company = (int)$this->getRequest()->getParam('company_id');
        $companyName = $this->getRequest()->getParam('company_name');
        if ($company > 0) {
            $arr = array('id' => $company, 'name' => $companyName);
            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::API_COMPANY,
                $company
            );
            Mage::app()->getConfig()->saveConfig(
                Mageflow_Connect_Model_System_Config::API_COMPANY_NAME,
                serialize($arr)
            );
        }
        $client = $this->getApiClient();
        $client->setCompany($company);
        $response = json_decode($client->get('project'));
        $this->getLogger()->log(
            $response,
            __METHOD__,
            __LINE__
        );

        $block = Mage::getBlockSingleton('adminhtml/template');
        $response->register_query_url = Mage::helper('adminhtml')->getUrl(
            'mageflow_connect/ajax/registerInstance',
            array('form_key' => $block->getFormKey())
        ) . '?isAjax=true';

        $this->getLogger()->log(
            $response->register_query_url,
            __METHOD__,
            __LINE__
        );
        $jsonData = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * Registers development instance at MageFlow
     */
    public function registerInstanceAction()
    {
        $this->getLogger()->log(
            $this->getRequest()->getParams(),
            __METHOD__,
            __LINE__
        );

        $client = $this->getApiClient();
        $rawResponse =
            $client->post(
                'instance',
                array(
                     'company' => $this->getRequest()->getParam(
                         'mageflow_connect_api_company'
                     ),
                     'project' => $this->getRequest()->getParam(
                         'mageflow_connect_api_project'
                     ),
                     'type' => 'development',
                     'instance_key' => $this->getRequest()->getParam(
                         'mageflow_connect_api_instance_key'
                     ),
                     'base_url' => Mage::getBaseUrl(
                         Mage_Core_Model_Store::URL_TYPE_WEB,
                         true
                     ),
                     'api_url' => Mage::getBaseUrl(
                         Mage_Core_Model_Store::URL_TYPE_WEB,
                         true
                     ) . 'api/rest/',
                )
            );

        $this->getLogger()->log(
            $rawResponse,
            __METHOD__,
            __LINE__
        );

        $response = json_decode($rawResponse);

        $this->getLogger()->log(
            $response,
            __METHOD__,
            __LINE__
        );

        //get instance key from client as  response
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_PROJECT,
            $this->getRequest()->getParam('mageflow_connect_api_project')
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_PROJECT_NAME,
            $this->getRequest()->getParam('project_name')
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY,
            $response->items[0]->instance_key
        );


        $jsonData = Mage::helper('core')->jsonEncode($response->items[0]);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }


    /**
     * Creates Oauth consumer on Magento's side
     * and passes that info to Mageflow so that
     * Mageflow can connect to that Magento
     */
    public function createoauthAction()
    {
        $this->getLogger()->log(
            $this->getRequest()->getParams(),
            __METHOD__,
            __LINE__
        );
        $instanceKey = $this->getRequest()->getParam(
            'mageflow_connect_instance_key'
        );
        $this->getLogger()->log(
            'instance key:' . $instanceKey,
            __METHOD__,
            __LINE__
        );

        $oauthHelper = Mage::helper('mageflow_connect/oauth');
        $response = $oauthHelper->createOAuthConsumer($instanceKey);

        $jsonData = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * test connection to MF API
     */
    public function testapiAction()
    {
        $this->getLogger()->log('testing');
        $client = $this->getApiClient();
        $response = json_decode($client->get('whoami'));
        $this->getLogger()->log(
            $response,
            __METHOD__,
            __LINE__
        );
        $jsonData = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

}
