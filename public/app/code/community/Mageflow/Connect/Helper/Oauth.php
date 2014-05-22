<?php
require_once 'Mageflow/Connect/Module.php';
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
 * Oauth.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Oauth
 * MageFlow OAuth helper that deals with setting up Magento OAuth consumer
 * as well as returning MageFlow API client instance
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Oauth extends Mage_Core_Helper_Abstract
{

    public function __construct()
    {
        $m = new \Mageflow\Connect\Module();
    }

    /**
     * Helper method to create OAuth consumer
     *
     * @param $instanceKey
     *
     * @return mixed|stdClass
     */
    public function createOAuthConsumer($instanceKey)
    {
        $response = new stdClass();
        $response->success = false;
        try {
            /**
             * @var Mage_Oauth_Model_Consumer
             */
            $adminUserName = $instanceKey . '_oauth';
            Mage::helper('mageflow_connect/log')->log($adminUserName);

            $adminUserModel = Mage::getModel('admin/user');
            $adminUserModel->loadByUsername($adminUserName);
            if ($adminUserModel->getId() <= 0) {
                $adminUserModel->setEmail(
                    $adminUserName . '@oauth.mageflow.com'
                );
                $adminUserModel->setUsername($adminUserName);
                $adminUserModel->setFirstname('Mageflow');
                $adminUserModel->setLastname('Consumer');
                $password = Mage::helper('mageflow_connect')->randomHash();
                $adminUserModel->setPassword($password);
                $adminUserModel->save();

                $rootRoleModel = Mage::getModel('admin/role')->getCollection()
                    ->addFilter('role_type', 'G')->addFilter('tree_level', 1)
                    ->getFirstItem();


                $adminRoleModel = Mage::getModel('admin/role');
                $adminRoleModel->setUserId($adminUserModel->getId());
                $adminRoleModel->setParentId($rootRoleModel->getId());
                $adminRoleModel->setRoleType('U');
                $adminRoleModel->setTreeLevel(2);
                $adminRoleModel->setRoleName($adminUserModel->getUsername());
                $adminRoleModel->save();

            }
            //set API2 user role
            //add creation of admin role of it does not exist
            $apiAclRole = Mage::getModel('api2/acl_global_role')->getCollection()->addFilter('role_name', 'Admin')
                ->getFirstItem();

            if (!($apiAclRole instanceof Mage_Api2_Model_Acl_Global_Role)
                || !$apiAclRole->getId()
            ) {
                $apiAclRole->setRoleName('Admin');
                $apiAclRole->save();
                /**
                 * @var Mage_Api2_Model_Acl_Global_Rule
                 */
                $rule = Mage::getModel('api2/acl_global_rule');
                $collection = $rule->getCollection();
                $ruleItem = $collection->addFilterByRoleId($apiAclRole->getId())
                    ->getFirstItem();
                $ruleItem->setRoleId($apiAclRole->getId());
                $ruleItem->setResourceId(
                    Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL
                );
                $ruleItem->save();
            }

            //save admin user to role relation
            Mage::getModel('api2/acl_global_role')
                ->getResource()->saveAdminToRoleRelation(
                    $adminUserModel->getId(),
                    $apiAclRole->getId()
                );


            $apiAclAttribute = Mage::getModel('api2/acl_filter_attribute')
                ->getCollection()
                ->addFilter('user_type', 'admin')->getFirstItem();
            if (!($apiAclAttribute instanceof
                    Mage_Api2_Model_Acl_Filter_Attribute)
                || !$apiAclAttribute->getId()
            ) {
                $apiAclAttribute->setUserType('admin');
                $apiAclAttribute->setResourceId(
                    Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL
                );
                $apiAclAttribute->save();
            }
            $oauthConsumerModel = Mage::getModel('oauth/consumer');
            //create admin user with the same username
            $oauthConsumerModel->load($adminUserName, 'name');
            if ($adminUserModel->getId() > 0
                && $oauthConsumerModel->getId() <= 0
            ) {
                $oauthConsumerModel->setName($adminUserName);
                $oauthConsumerModel->setKey(
                    md5(Mage::helper('mageflow_connect')->randomHash())
                );
                $oauthConsumerModel->setSecret(
                    md5(Mage::helper('mageflow_connect')->randomHash())
                );
                $oauthConsumerModel->save();
                $oauthConsumerId = $oauthConsumerModel->getId();
                Mage::helper('mageflow_connect/log')->log(
                    'Created OAuth consumer with ID ' . $oauthConsumerId
                );
            }

            $token = Mage::getModel('oauth/token');
            $token->createRequestToken(
                $oauthConsumerModel->getId(),
                'http://escape.to.the.void/' . Mage::helper('mageflow_connect')
                    ->randomHash() . '/'
            );
            $token->authorize(
                $adminUserModel->getId(),
                Mage_Oauth_Model_Token::USER_TYPE_ADMIN
            );
            $token->convertToAccess();

            Mage::helper('mageflow_connect/log')->log(
                'Converted token to access token'
            );

            if ($oauthConsumerModel->getId() > 0) {
                //send registraton info and keys to MageFlow HERE
                $findClient = $this->getApiClient();
                $findRequest = 'find/Instance/instance_key/' . $instanceKey;
                Mage::helper('mageflow_connect/log')->log(
                    'Searching for existing entity: ' . $findRequest
                );

                $findResponse = $findClient->get($findRequest);
                $instanceData = json_decode($findResponse);
                Mage::helper('mageflow_connect/log')->log(
                    print_r($instanceData, true)
                );
                $instanceId = $instanceData->items[0]->id;

                if ($instanceId < 1) {
                    Mage::helper('mageflow_connect/log')->log(
                        'ERROR: Could not fetch
                        instance ID and cannot continue without it.'
                    );
                    $response->success = false;
                    $response->errrorMessage = "Could not retrieve instance ID";
                    return $response;
                }
                $key = $oauthConsumerModel->getKey();
                $data = array(
                    'consumer_key' => $oauthConsumerModel->getKey(),
                    'consumer_secret' => $oauthConsumerModel->getSecret(),
                    'token' => $token->getToken(),
                    'token_secret' => $token->getSecret(),
//                    'api_url'         =>
//                        Mage::getBaseUrl(
//                            Mage_Core_Model_Store::URL_TYPE_WEB,
//                            true
//                        )
//                        . 'api/rest/',
//                    'base_url'        => Mage::getBaseUrl(
//                        Mage_Core_Model_Store::URL_TYPE_WEB,
//                        true
//                    ),
                );

                $client = $this->getApiClient();

                Mage::helper('mageflow_connect/log')->log(
                    'Registering OAuth consumer at MageFlow'
                );

                $encodedResponse = $client->put(
                    'access/' . $instanceKey,
                    $data
                );

                $response = json_decode($encodedResponse);

                Mage::helper('mageflow_connect/log')->log(
                    'Response: ' . print_r($response, true)
                );

                if (!empty($response)) {
                    $response->success = true;
                }
            }
        } catch (Exception $e) {
            Mage::helper('mageflow_connect/log')->log($e->getMessage());
            $response->success
                = false;
            $response->errormessage
                = $e->getMessage();
        }

        return $response;
    }

    /**
     * Returns MageFlow API client instance
     *
     * @return \Mageflow\Connect\Model\Api\Mageflow\Client
     */
    public function getApiClient()
    {
        Mage::helper('mageflow_connect/log')->log(
            'Creating and configuring MageFlow API client',
            __METHOD__,
            __LINE__
        );
        $configuration = new stdClass();

        Mage::app()->getConfig()->cleanCache();

        $configuration->_consumerKey = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY
        );

        $configuration->_consumerSecret = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET
        );

        $configuration->_token = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN
        );

        $configuration->_tokenSecret = Mage::app()->getStore()->getConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET
        );

        $companyArr = unserialize(
            \Mage::app()->getStore()->getConfig(
                \Mageflow_Connect_Model_System_Config::API_COMPANY_NAME
            )
        );

        $configuration->_company = $companyArr['id'];

        $configuration->_project = \Mage::app()->getStore()->getConfig(
            \Mageflow_Connect_Model_System_Config::API_PROJECT
        );

        $configuration->_instanceKey = \Mage::app()->getStore()
            ->getConfig(
                \Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY
            );

        $client
            = new \Mageflow\Connect\Model\Api\Mageflow\Client($configuration);

        Mage::helper('mageflow_connect/log')->log(
            $configuration,
            __METHOD__,
            __LINE__
        );

        return $client;
    }
}