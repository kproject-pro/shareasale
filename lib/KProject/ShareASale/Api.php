<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see the license tag below.
 *
 * @author    KProject <support@kproject.pro>
 * @license   http://www.gnu.org/licenses/ GNU General Public License, version 3
 * @copyright 2016 KProject.pro
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
        $queryParams = $this->getTransactionHelper()->getNewTransactionParams($order);
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);

        return $client->request();
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
            'date'        => $this->getOrderDate($order),
            'ordernumber' => $order->getIncrementId(),
            'reason'      => 'Full Refund'
        );
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);

        return $client->request();
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
            'date'        => $this->getOrderDate($order),
            'ordernumber' => $order->getIncrementId(),
            'newamount'   => $order->getGrandTotal() - $order->getTotalRefunded(),
            'newcomment'  => 'Partial Refund'
        );
        $queryParams = array_merge($queryParams, $params);

        $client = $this->getBaseClientSetup($action);
        $client->getUri()->addReplaceQueryParameters($queryParams);

        return $client->request();
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
    private function getSignature($actionVerb)
    {
        $APIToken     = $this->credentials->getToken();
        $APISecretKey = $this->credentials->getSecretKey();
        $sig          = $APIToken . ':' . $this->getTimeStamp() . ':' . $actionVerb . ':' . $APISecretKey;

        return hash('sha256', $sig);
    }

    /**
     * @return string
     */
    private function getTimeStamp()
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

    /**
     * @return KProject_ShareASale_Helper_Transaction|Mage_Core_Helper_Abstract
     */
    private function getTransactionHelper()
    {
        return Mage::helper('kproject_sas/transaction');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    private function getOrderDate(Mage_Sales_Model_Order $order)
    {
        $date = new Zend_Date($order->getCreatedAtDate());
        $date->setTimezone('EST5EDT');
        $orderDate = date('m/d/Y', $date->getTimestamp());

        return $orderDate ? $orderDate : '';
    }
}
