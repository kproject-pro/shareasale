<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Transaction extends KProject_ShareASale_Helper_Data
{

    /**
     * Create new transaction using the ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function create(Mage_Sales_Model_Order $order)
    {
        $parameters = Mage::getSingleton('kproject_sas/session')->getParameters();
        if (!$this->isActive($order->getStoreId()) || !$parameters) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);

        try {
            $response = $api->createNewTransaction($order, $parameters);
        } catch (Exception $e) {
            $response = null;
            Mage::logException($e);
        }

        return $response;
    }

    /**
     * Fully void a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function void(Mage_Sales_Model_Order $order)
    {
        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);
        $parameters  = Mage::getSingleton('kproject_sas/session')->getParameters();

        try {
            $response = $api->voidTransaction($order, $parameters);
        } catch (Exception $e) {
            $response = null;
            Mage::getSingleton('core/session')->addError(
                'Error when calling ShareASale Void Transaction API: ' . $e->getMessage()
            );
        }

        return $response;
    }

    /**
     * Make an edit to a transaction using ShareASale API
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool | Mage_Sales_Model_Order
     */
    public function edit(Mage_Sales_Model_Order $order)
    {
        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        $credentials = $this->getCredentials($order->getStoreId());
        $api         = new KProject_ShareASale_Api($credentials);
        $parameters  = Mage::getSingleton('kproject_sas/session')->getParameters();

        try {
            $response = $api->editTransaction($order, $parameters);
        } catch (Exception $e) {
            $response = null;
            Mage::getSingleton('core/session')->addError(
                'Error when calling ShareASale Edit Transaction API: ' . $e->getMessage()
            );
        }

        return $response;
    }

    /**
     * Returns a list of parameters to use for
     * new order transactions
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function getNewTransactionParams(Mage_Sales_Model_Order $order)
    {
        return array_merge(
            array(
                'transtype'   => 'sale',
                'tracking'    => $order->getIncrementId(),
                'amount'      => $order->getSubtotal() + $order->getDiscountAmount(),
                'couponcode'  => $order->getCouponCode(),
                'newcustomer' => Mage::helper('kproject_sas/customer')->getIsNewParam($order->getCustomerId()),
                'currency'    => $order->getOrderCurrencyCode(),
            ),
            Mage::helper('kproject_sas/product')->getItemParams($order)
        );
    }
}
