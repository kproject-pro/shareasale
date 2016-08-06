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

        if (!$magentoOrder || Mage::registry('kproject_sas_observer_disable')) { //todo-konstantin double check this
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
        /** @var Mage_Sales_Model_Order $magentoOrder */
        $magentoOrder = $observer->getEvent()->getData('order');

        if (!$magentoOrder || Mage::registry('kproject_sas_observer_disable')) {
            return $this;
        }

        //todo: check if partial or full refund needed

        return $this;
    }

    /**
     * Helper that saves the cookies in to session to be pulled
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function setParameters(Varien_Event_Observer $observer)
    {
        //todo-sg: get request
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
