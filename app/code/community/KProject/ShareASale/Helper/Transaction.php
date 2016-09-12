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
        $parameters = $this->getSession()->getParameters();
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
        $parameters  = $this->getSession()->getParameters();

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
        $parameters  = $this->getSession()->getParameters();

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

    /**
     * @return KProject_ShareASale_Model_Session
     */
    private function getSession()
    {
        return Mage::getSingleton('kproject_sas/session');
    }
}
