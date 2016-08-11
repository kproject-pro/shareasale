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
            || Mage::registry('kproject_sas_observer_disable') //todo-konstantin: double check this
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
        $creditMemo  = $observer->getData('creditmemo');

        if (!$creditMemo || Mage::registry('kproject_sas_observer_disable')) {
            return $this;
        }
        $magentoOrder = $creditMemo->getOrder();
        if ($magentoOrder->getTotalRefunded() >= $magentoOrder->getGrandTotal()) {
            $this->getTransactionHelper()->void($magentoOrder); //todo-konstantin: try credit memo from invoice screen
        } else {
            $this->getTransactionHelper()->edit($magentoOrder);
        }

        return $this;
    }

    /**
     * Helper that saves the cookies in to session to be pulled
     * later on in place order observer
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function setParameters(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('kproject_sas')->newTransactionViaApiEnabled()) {
            return $this;
        }

        //todo-konstantin: get request, make sure we are not in admin
        $event = $observer->getEvent();
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getData('controller_action')->getRequest();
        $userId  = $request->getParam('userID');

        //Mage::getSingleton('core/session')->setData('kproject_sas_parameters');
        return $this;
    }

    /**
     * @return KProject_ShareASale_Helper_Transaction
     */
    private function getTransactionHelper()
    {
        return Mage::helper('kproject_sas/transaction');
    }
}
