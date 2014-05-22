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
 * Client.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

namespace Mageflow\Connect\Model\Api\Mageflow;

use Mageflow\Connect\Model\Api\AbstractClient;
use Mageflow\Connect\Model\Oauth;

/**
 * Client
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Client extends AbstractClient
{

    const REQUEST_TIMEOUT = 200;

    /**
     * api url
     *
     * @var
     */
    private $apiUrl;

    /**
     * Class constructor
     *
     * @param \stdClass $configuration
     */
    public function __construct(\stdClass $configuration = null)
    {
        parent::__construct($configuration);
    }

    /**
     * Returns MageFlow API URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        if (is_null($this->apiUrl)) {
            $this->apiUrl = \Mage::app()->getStore()->getConfig(
                \Mageflow_Connect_Model_System_Config::API_URL
            );
        }
        return $this->apiUrl;
    }

    /**
     * fix uri
     *
     * @param       $resource
     *
     * @param array $params
     *
     * @return string
     */
    private function fixUri($resource, $params = array())
    {
        $uri = rtrim(
                \Mage::getStoreConfig('mageflow_connect/advanced/api_url'), '/'
            ) . '/' . ltrim($resource, '/');

        $this->getLogger()->debug($uri);
        $this->getLogger()->debug($params);

        if (isset($params['id']) && $params['id']) {
            $uri .= '/' . $params['id'];
            unset($params['id']);
        }

        if ($resource != 'company' && isset($this->_company)
            && $this->_company
        ) {
            $uri .= '/company/' . $this->_company;
        }

        if (isset($params['project']) && $params['project']) {
            $uri .= '/project/' . $params['project'];
            unset($params['project']);
        }
        return $uri;
    }

    /**
     * get
     *
     * @param       $resource
     * @param array $params
     *
     * @return string
     */
    public function get($resource, $params = array())
    {
        $this->getLogger()->debug($resource);
        $this->getLogger()->debug($params);

        $uri = $this->fixUri($resource, $params);

        return $this->makeHttpRequest(
            $uri, \Zend_Http_Client::GET, $params
        );

    }

    /**
     * post
     *
     * @param       $resource
     * @param array $data
     *
     * @return null|string
     */
    public function post($resource, $data = array())
    {
        $uri = $this->fixUri($resource, $data);
        return $this->makeHttpRequest($uri, \Zend_Http_Client::POST, $data);

    }

    /**
     * put
     *
     * @param       $resource
     * @param array $data
     *
     * @return null|string
     */
    public function put($resource, $data = array())
    {
        if (isset($this->_company) && $this->_company) {
            $data['company'] = $this->_company;
        }
        $uri = $this->fixUri($resource, $data);
        return $this->makeHttpRequest($uri, \Zend_Http_Client::PUT, $data);
    }


    /**
     * make Http Request
     *
     * @param       $uri
     * @param       $method
     * @param array $data
     *
     * @return null|string
     */
    private function makeHttpRequest($uri, $method, $data = array())
    {
        try {

            $this->getLogger()->debug(
                sprintf('Making %s request to %s', $method, $uri)
            );

            $token = new \Zend_Oauth_Token_Access();
            $token->setToken($this->getToken());
            $token->setTokenSecret($this->getTokenSecret());

            $client = $token->getHttpClient(
                array(
                    'consumerKey' => $this->getConsumerKey(),
                    'consumerSecret' => $this->getConsumerSecret()
                )
            );

            $client->setUri($uri);

            $client->setHeaders(
                array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                )
            );

            if ($method == \Zend_Http_Client::GET && sizeof($data) > 0) {
                $client->setParameterGet($data);
            } elseif (sizeof($data) > 0) {
                $client->setParameterPost($data);
                $client->setEncType(\Zend_Http_Client::ENC_URLENCODED);
            }

            //FIXME add params to zend http client
            $contextParams = array(
                'http' => array(
                    'timeout' => Client::REQUEST_TIMEOUT
                ),
                'ssl' => array(
                    'verify_peer' => true,
                    'capath' => '/etc/ssl/certs',
                    'verify_depth' => 10,
                    'CN_match' => 'app.mageflow.com',
                    'disable_compression' => true,
                    'SNI_enabled' => true,
                    'ciphers' =>
                        'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4'
                )
            );

            $adapter = new \Zend_Http_Client_Adapter_Socket();
            $adapter->setStreamContext(
                stream_context_create($contextParams)
            );

            $client->setAdapter($adapter);

            $response = $client->request($method);

            $this->getLogger()->debug($response->getBody());

            return $response->getBody();
        } catch (\Exception $ex) {
            $this->getLogger()->error($ex->getMessage());
            $this->getLogger()->error($ex->getTraceAsString());
        }
        return null;
    }

}
