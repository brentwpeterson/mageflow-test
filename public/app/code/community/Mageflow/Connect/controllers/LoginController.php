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
 * LoginController.php
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
 * Mageflow_Connect_LoginController
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Controller
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_LoginController
    extends Mageflow_Connect_Controller_AbstractController
{
    /**
     * public actions
     *
     * @var array
     */
    public $_publicActions = array('index', 'mfloginAction');

    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * index action
     */
    public function indexAction()
    {

    }

    /**
     * Make request to MF API to verify one-time token
     * and log in admin user if the token and e-mail are valid
     */
    public function mfloginAction()
    {
        $hash = $this->getRequest()->getParam('hash');
        $id = $this->getRequest()->getParam('id');
        $this->getLogger()->log($hash);
        $client = $this->getApiClient();
        $result = json_decode($client->get('whois', array('id' => $id)), true);

        $this->getLogger()->log(print_r($result, true));

        if ($hash !== $result['items']['auth_hash']) {
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        $email = $result['items']['email'];

        $adminUserCollection = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('email', $email)
            ->addFieldToFilter('is_active', 1);
        $user = $adminUserCollection->getFirstItem();

        $session = Mage::getSingleton('admin/session');
        $this->getLogger()->log(
            sprintf(
                '%s(%s): %s', __METHOD__, __LINE__,
                print_r($session->getSessionId(), true)
            )
        );
        Mage::dispatchEvent(
            'admin_user_authenticate_before',
            array(
                 'username' => $user->getUsername(),
                 'user'     => $user
            )
        );

        Mage::dispatchEvent(
            'admin_user_authenticate_after',
            array(
                 'username' => $user->getUsername(),
                 'password' => null,
                 'user'     => $user,
                 'result'   => true,
            )
        );
        $session->setUser($user);
//        $session->setIsFirstVisit(true);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        Mage::dispatchEvent(
            'admin_session_user_login_success', array('user' => $user)
        );
        session_write_close();
        $this->_redirect('adminhtml/dashboard/index');
        return;
    }
}
