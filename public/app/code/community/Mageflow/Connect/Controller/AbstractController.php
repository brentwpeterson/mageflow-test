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
 * AbstractController.php
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

//needed for custom classcloading and namespaces
require_once 'Mageflow/Connect/Module.php';

/**
 * Mageflow_Connect_Controller_AbstractController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Controller_AbstractController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Class constuctor
     */
    public function _construct()
    {
        //include Mageflow client lib and its autoloader
        $m = new \Mageflow\Connect\Module();
    }

    /**
     * logger
     *
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     * Returns logger helper instance
     *
     * @return Mageflow_Connect_Helper_Log
     */
    public function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = Mage::helper('mageflow_connect/log');
        }
        return $this->_logger;
    }

    /**
     * Returns MageFlow API client instance with
     * authentication fields filled in
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        $client = Mage::helper('mageflow_connect/oauth')->getApiClient();

        return $client;
    }
}
