<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Api
{

    const API_VERSION = 2.8;
    const ACTION_NEW  = 'new';
    const ACTION_VOID = 'void';
    const ACTION_EDIT = 'edit';

    /** @var KProject_ShareASale_Credentials */
    private $credentials;

    public function __construct(KProject_ShareASale_Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Creates a new ShareASale API transaction
     *
     * @param Mage_Sales_Model_Order $order
     * @param array                  $params - optional extra params that can rewrite the originals
     *
     * @return Zend_Http_Response
     */
    public function createNewTransaction(Mage_Sales_Model_Order $order, $params = array())
    {
        $action      = self::ACTION_NEW;
        $total       = $order->getGrandTotal();
        $queryParams = array(
            'transtype' => 'sale',
            'tracking'  => $order->getIncrementId(),
            'amount'    => $total
        );
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);
        $response = $client->request();

        return $response;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param array                  $params - optional extra params that can rewrite the originals
     *
     * @return Zend_Http_Response
     */
    public function voidTransaction(Mage_Sales_Model_Order $order, $params = array())
    {
        $action      = self::ACTION_VOID;
        $queryParams = array(
            'date'        => date('m/d/Y', $order->getCreatedAtDate()->getTimestamp()),
            'ordernumber' => $order->getIncrementId(),
            'reason'      => 'Full Refund'
        );
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);
        $response = $client->request();

        return $response;
    }

    /**
     * @param Mage_Sales_Model_Order $kOrder
     * @param array                  $params - optional extra params that can rewrite the originals
     *
     * @return Zend_Http_Response
     */
    public function editTransaction(Mage_Sales_Model_Order $order, $params = array())
    {
        $action      = self::ACTION_EDIT;
        $queryParams = array(
            'date'        => date('m/d/Y', $order->getCreatedAtDate()->getTimestamp()),
            'ordernumber' => $order->getIncrementId(),
            'newamount'   => $order->getGrandTotal(), //todo-konstantin: check on this
            'newcomment'  => 'Partial Refund'
        );
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);
        $response = $client->request();

        return $response;
    }

    /**
     * @param string $action
     *
     * @return Zend_Http_Client
     */
    protected function getBaseClientSetup($action)
    {
        $client = $this->getZendClient();
        $uri    =
            'https://api.shareasale.com/w.cfm' .
            '?merchantId=' . $this->credentials->getMerchantId() .
            '&token=' . $this->credentials->getToken() .
            '&version=' . self::API_VERSION .
            '&action=' . $action;

        return $client
            ->setHeaders('x-ShareASale-Date', $this->getTimeStamp())
            ->setHeaders('x-ShareASale-Authentication', $this->getSignature($action))
            ->setMethod()
            ->setUri($uri);
    }

    /**
     * Returns the signature to pass in the
     * header for authentication purposes
     *
     * @param string $actionVerb
     *
     * @return string
     */
    public function getSignature($actionVerb)
    {
        $APIToken     = $this->credentials->getToken();
        $APISecretKey = $this->credentials->getSecretKey();
        $sig          = $APIToken . ':' . $this->getTimeStamp() . ':' . $actionVerb . ':' . $APISecretKey;

        return hash('sha256', $sig);
    }

    /**
     * @return string
     */
    public function getTimeStamp()
    {
        return gmdate(DATE_RFC1123);
    }

    /**
     * @return Zend_Http_Client
     */
    public function getZendClient()
    {
        return new Zend_Http_Client();
    }
}
