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
 * AbstractClient.php
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

namespace Mageflow\Connect\Model\Api;

use Mageflow\Connect\Model\AbstractModel;

/**
 * AbstractClient
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 *
 * @method string getToken()
 * @method string getTokenSecret()
 * @method string getConsumerKey()
 * @method string getConsumerSecret()
 */
abstract class AbstractClient extends AbstractModel
{

    /**
     * api url
     *
     * @var null
     */
    protected $_apiUrl = null;

    /**
     * token
     *
     * @var
     */
    protected $_token;

    /**
     * token secret
     *
     * @var
     */
    protected $_tokenSecret;

    /**
     * consumer key
     *
     * @var
     */
    protected $_consumerKey;

    /**
     * consumer secret
     *
     * @var
     */
    protected $_consumerSecret;

    /**
     * logger
     *
     * @var
     */
    private $_logger;

    /**
     * Class constructor
     *
     * @param \stdClass $configuration
     *
     * @return \Mageflow\Connect\Model\Api\AbstractClient
     */
    public function __construct(\stdClass $configuration = null)
    {
        if (!is_null($configuration)) {
            foreach ($configuration as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    /**
     * get logger
     *
     * @return \Mageflow\Connect\Helper\Logger
     */
    protected function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = new \Mageflow\Connect\Helper\Logger();
        }
        return $this->_logger;
    }

}
