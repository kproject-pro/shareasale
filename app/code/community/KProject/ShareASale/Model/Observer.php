<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Model_Observer
{
    public function newOrderTransaction(Varien_Event_Observer $observer)
    {
        if (!class_exists('KProject_ShareASale_Api')) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $magentoOrder */
        $magentoOrder = $observer->getEvent()->getData('order');
        $credentials  = Mage::helper('kproject_shareasale')->getCredentials($magentoOrder->getStoreId());
        $api          = new KProject_ShareASale_Api($credentials);
        $success      = $api->createNewTransaction($magentoOrder);

        return $this;
    }
}
