<?php
namespace PayPal\Transport;

use PayPal\Core\PayPalHttpConfig;
use PayPal\Core\PayPalHttpConnection;
use PayPal\Exception\PayPalConfigurationException;
use PayPal\Exception\PayPalConnectionException;
use Paypal\Handler\IPayPalHandler;
use PayPal\Rest\ApiContext;

/**
 * Class PayPalRestCall
 *
 * @package PayPal\Transport
 */
class PayPalRestCall
{

    /**
     * API Context
     *
     * @var ApiContext
     */
    private $apiContext;


    /**
     * Default Constructor
     *
     * @param ApiContext $apiContext
     */
    public function __construct(ApiContext $apiContext)
    {
        $this->apiContext = $apiContext;
    }

    /**
     * @param array $handlers Array of handlers
     * @param string $path Resource path relative to base service endpoint
     * @param string $method HTTP method - one of GET, POST, PUT, DELETE, PATCH etc
     * @param string $data Request payload
     * @param array $headers HTTP headers
     * @return false|string
     * @throws PayPalConnectionException
     * @throws PayPalConfigurationException
     */
    public function execute($handlers, $path, $method, $data = '', $headers = array())
    {
        $config = $this->apiContext->getConfig();
        $httpConfig = new PayPalHttpConfig(null, $method, $config);
        $headers = $headers ? $headers : array();
        $httpConfig->setHeaders($headers +
            array(
                'Content-Type' => 'application/json'
            )
        );


        /** @var IPayPalHandler $handler */
        foreach ($handlers as $handler) {
            if (!is_object($handler)) {
                $fullHandler = "\\" . (string)$handler;
                $handler = new $fullHandler($this->apiContext);
            }
            $handler->handle($httpConfig, $data, array('path' => $path, 'apiContext' => $this->apiContext));
        }
        $connection = new PayPalHttpConnection($httpConfig, $config);
        return $connection->execute($data);
    }
}
