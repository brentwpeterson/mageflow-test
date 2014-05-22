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
 * Observer.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
/**
 * Mageflow_Connect_Model_Admin_Observer
 * This class extends Mage_Admin_Model_Observer
 *
 * @category   MFX
 * @package    Application
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Admin_Observer extends Mage_Admin_Model_Observer
{

    /**
     * This method interferes to adminhtml controllers and
     * helps to login by Oauth
     *
     * @param Varien_Event_Observer $observer
     *
     * @return bool|void
     */
    public function actionPreDispatchAdmin($observer)
    {
        $openActions = array(
            'mflogin'
        );
        $session = Mage::getSingleton('admin/session');
        $request = Mage::app()->getRequest();
        $requestedActionName = $request->getActionName();
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
            $session->refreshAcl();
            return;
        }
        parent::actionPreDispatchAdmin($observer);
    }

}
