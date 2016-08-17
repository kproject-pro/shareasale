<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
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

        if (!$magentoOrder
            || Mage::registry('kproject_sas_observer_disable')
            || !Mage::helper('kproject_sas')->newTransactionViaApiEnabled($magentoOrder->getStoreId())
        ) {
            return $this;
        }

        $this->getTransactionHelper()->create($magentoOrder);

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
            $this->getTransactionHelper()->void($magentoOrder);
        } else {
            $this->getTransactionHelper()->edit($magentoOrder);
        }

        return $this;
    }

    /**
     * Checks if it's a full cancellation of the order
     * or a partial one
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

        return $qtyCancelled == $order->getTotalQtyOrdered();
    }

    /**
     * @return KProject_ShareASale_Helper_Transaction
     */
    private function getTransactionHelper()
    {
        return Mage::helper('kproject_sas/transaction');
    }
}
