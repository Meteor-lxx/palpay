<?php

namespace PayPal\Api;

use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;

/**
 * Class FuturePayment
 *
 * @package PayPal\Api
 */
class FuturePayment extends Payment
{

    /**
     * Extends the Payment object to create future payments
     *
     * @param ApiContext|null $apiContext
     * @param PayPalRestCall|null $restCall
     * @return $this
     */
    public function create(ApiContext $apiContext = null, PayPalRestCall $restCall = null)
    {
        if ($apiContext == null) {
            $apiContext = new ApiContext(self::$credential);
        }
        $headers = array();
        if ($restCall != null) {
            $headers = array(
                'PAYPAL-CLIENT-METADATA-ID' => $restCall
            );
        }
        $payLoad = $this->toJSON();
        $call = new PayPalRestCall($apiContext);
        $json = $call->execute(
            array('PayPal\Handler\RestHandler'),
            "/v1/payments/payment",
            "POST",
            $payLoad,
            $headers
        );
        $this->fromJson($json);

        return $this;

    }

    /**
     * Get a Refresh Token from Authorization Code
     *
     * @param $authorizationCode
     * @param ApiContext $apiContext
     * @return string|null refresh token
     */
    public static function getRefreshToken($authorizationCode, $apiContext = null)
    {
        $apiContext = $apiContext ? $apiContext : new ApiContext(self::$credential);
        $credential = $apiContext->getCredential();
        return $credential->getRefreshToken($apiContext->getConfig(), $authorizationCode);
    }

}
