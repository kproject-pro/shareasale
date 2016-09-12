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
class KProject_ShareASale_Model_Observer
{

    /**
     * Send new order transaction to the API
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function newOrderTransaction(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $magentoOrder */
        $magentoOrder = $observer->getEvent()->getData('order');
        $parameters   = Mage::getSingleton('kproject_sas/session')->getParameters();

        if (!$magentoOrder
            || Mage::registry('kproject_sas_observer_disable')
            || !Mage::helper('kproject_sas')->newTransactionViaApiEnabled($magentoOrder->getStoreId())
            || empty($parameters)
            || $this->isCheckout()
        ) {
            return $this;
        }

        $response = $this->getTransactionHelper()->create($magentoOrder);
        Mage::helper('kproject_sas/status')->setKOrderStatus(
            $magentoOrder,
            KProject_ShareASale_Helper_Status::STATUS_SUCCESS,
            $response
        );

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function refundOrderTransaction(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditMemo */
        $creditMemo = $observer->getData('creditmemo');

        if (!$creditMemo || Mage::registry('kproject_sas_observer_disable')) {
            return $this;
        }

        $magentoOrder = $creditMemo->getOrder();
        if ($this->isFullCancellation($magentoOrder)) {
            $response = $this->getTransactionHelper()->void($magentoOrder);
            $status   = KProject_ShareASale_Helper_Status::STATUS_FULL_REFUND;
        } else {
            $response = $this->getTransactionHelper()->edit($magentoOrder);
            $status   = KProject_ShareASale_Helper_Status::STATUS_PARTIAL_REFUND;
        }
        Mage::helper('kproject_sas/status')->setKOrderStatus($magentoOrder, $status, $response);

        return $this;
    }

    /**
     * Helper that saves the GET params into session to be pulled
     * later when an order is placed. Can be rewritten easily
     * with newer values.
     *
     * @note This is a helper observer that will not ship with
     *       the initial version as that version was meant to
     *       utilize the script tag on the success page instead
     *       of the new transaction API
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function setParameters(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('kproject_sas')->newTransactionViaApiEnabled()
            || Mage::app()->getStore()->isAdmin()
        ) {
            return $this;
        }

        /** @var Mage_Core_Controller_Request_Http $request */
        $request  = $observer->getData('controller_action')->getRequest();
        $userKey  = $this->getTransactionHelper()->getAffiliateIdentifierKey();
        $clickKey = $this->getTransactionHelper()->getClickIdentifierKey();
        $userId   = $request->getParam($userKey);
        $clickId  = $request->getParam($clickKey);

        if ($userId && $clickId) {
            Mage::getSingleton('kproject_sas/session')->setParameters(
                array(
                    $userKey  => $userId,
                    $clickKey => $clickId
                )
            );
        }

        return $this;
    }

    /**
     * Checks if it's a full cancellation of the order
     * or a partial one
     * - All product qty's were refunded
     * - Amount refunded >= affiliate amount
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    private function isFullCancellation(Mage_Sales_Model_Order $order)
    {
        $qtyCancelled = 0;
        $orderItems   = $order->getItemsCollection();
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && $orderItem->getQtyCanceled() + $orderItem->getQtyRefunded() > 0
                && !$orderItem->getIsVirtual()
            ) {
                $qtyCancelled += intval($orderItem->getQtyCanceled()) + intval($orderItem->getQtyRefunded());
            }
        }

        $quantityRefunded = $qtyCancelled == $order->getTotalQtyOrdered();
        $amountRefunded   = $order->getTotalRefunded() >= ($order->getSubtotal() + $order->getDiscountAmount());

        return $quantityRefunded && $amountRefunded;
    }

    /**
     * Checks if current observer is firing
     * from the checkout page
     *
     * @return bool
     */
    private function isCheckout()
    {
        return strpos(Mage::app()->getRequest()->getRequestUri(), 'saveOrder') !== false;
    }

    /**
     * @return KProject_ShareASale_Helper_Transaction
     */
    private function getTransactionHelper()
    {
        return Mage::helper('kproject_sas/transaction');
    }
}
