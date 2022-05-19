<?php

namespace PayPal\Common;

use PayPal\Rest\ApiContext;
use PayPal\Rest\IResource;
use PayPal\Transport\PayPalRestCall;

/**
 * Class PayPalResourceModel
 * An Executable PayPalModel Class
 *
 * @property \PayPal\Api\Links[] links
 * @package PayPal\Common
 */
class PayPalResourceModel extends PayPalModel implements IResource
{

    /**
     * Sets Links
     *
     * @param \PayPal\Api\Links[] $links
     *
     * @return $this
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Gets Links
     *
     * @return \PayPal\Api\Links[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    public function getLink($rel)
    {
        foreach ($this->links as $link) {
            if ($link->getRel() == $rel) {
                return $link->getHref();
            }
        }
        return null;
    }

    /**
     * Append Links to the list.
     *
     * @param \PayPal\Api\Links $links
     * @return $this
     */
    public function addLink($links)
    {
        if (!$this->getLinks()) {
            return $this->setLinks(array($links));
        } else {
            return $this->setLinks(
                array_merge($this->getLinks(), array($links))
            );
        }
    }

    /**
     * Remove Links from the list.
     *
     * @param \PayPal\Api\Links $links
     * @return $this
     */
    public function removeLink($links)
    {
        return $this->setLinks(
            array_diff($this->getLinks(), array($links))
        );
    }


    /**
     * @param $url
     * @param $method
     * @param $payLoad
     * @param array $headers
     * @param null $apiContext
     * @param null $restCall
     * @param string[] $handlers
     * @return mixed
     * @throws \PayPal\Exception\PayPalConfigurationException
     * @throws \PayPal\Exception\PayPalConnectionException
     */
    protected static function executeCall($url, $method, $payLoad, $headers = array(), $apiContext = null, $restCall = null, $handlers = array('PayPal\Handler\RestHandler'))
    {
        //Initialize the context and rest call object if not provided explicitly
        $apiContext = $apiContext ? $apiContext : new ApiContext(self::$credential);
        $restCall = $restCall ? $restCall : new PayPalRestCall($apiContext);

        //Make the execution call
        return $restCall->execute($handlers, $url, $method, $payLoad, $headers);
    }

    /**
     * Updates Access Token using long lived refresh token
     *
     * @param string|null $refreshToken
     * @param ApiContext $apiContext
     * @return void
     */
    public function updateAccessToken($refreshToken, $apiContext)
    {
        $apiContext = $apiContext ? $apiContext : new ApiContext(self::$credential);
        $apiContext->getCredential()->updateAccessToken($apiContext->getConfig(), $refreshToken);
    }
}
