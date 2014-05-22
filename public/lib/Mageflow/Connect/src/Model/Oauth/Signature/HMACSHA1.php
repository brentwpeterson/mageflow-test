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
 * HMACSHA1.php
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

namespace Mageflow\Connect\Model\Oauth\Signature;

use Mageflow\Connect\Model\AbstractModel;
use Mageflow\Connect\Model\Oauth\Consumer;
use Mageflow\Connect\Model\Oauth\Token;
use Mageflow\Connect\Model\Oauth\Request;
use Mageflow\Connect\Model\Oauth\Util;

/**
 * HMACSHA1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class HMACSHA1 extends AbstractModel implements SignatureBuilder
{

    /**
     * name
     *
     * @var string
     */
    protected $name = "HMAC-SHA1";

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Needs to return the name of the Signature Method (ie HMAC-SHA1)
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Build up the signature
     * NOTE: The output of this function MUST NOT be urlencoded.
     * the encoding is handled in OAuthRequest when the final
     * request is serialized
     *
     * @param \Mageflow\Connect\Model\Oauth\Request|\Mageflow\Connect\Model\
     *      Oauth\Signature\OAuthRequest   $request
     * @param \Mageflow\Connect\Model\Oauth\Consumer|\Mageflow\Connect\Model\
     *      Oauth\Signature\OAuthConsumer $consumer
     * @param \Mageflow\Connect\Model\Oauth\Signature\OAuthToken|\Mageflow\
     *      Connect\Model\Oauth\Token       $token
     *
     * @return string
     */
    public function buildSignature(
        Request $request, Consumer $consumer,
        Token $token
    )
    {
        $base_string = $request->getSignatureBaseString();
        $request->setBaseString($base_string);

        $key_parts = array(
            $consumer->getSecret(),
            ($token) ? $token->getSecret() : ''
        );

        $key_parts = Util::urlencodeRFC3986($key_parts);
        $key = implode('&', $key_parts);

        return base64_encode(hash_hmac('sha1', $base_string, $key, true));
    }

    /**
     * Verifies that a given signature is correct
     *
     * @param Request  $request
     * @param Consumer $consumer
     * @param Token    $token
     * @param string   $signature
     *
     * @return bool
     */
    public function checkSignature(
        Request $request, Consumer $consumer,
        Token $token, $signature
    )
    {
        $builtSignature = $this->buildSignature($request, $consumer, $token);

        if (strlen($builtSignature) == 0 || strlen($signature) == 0) {
            return false;
        }

        if (strlen($builtSignature) != strlen($signature)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < strlen($signature); $i++) {
            $result |= ord($builtSignature{$i}) ^ ord($signature{$i});
        }

        return $result == 0;
    }

}