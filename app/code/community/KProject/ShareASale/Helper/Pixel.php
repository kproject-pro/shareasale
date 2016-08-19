<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Pixel extends KProject_ShareASale_Helper_Data
{

    /**
     * Create base client
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return Zend_Http_Client
     */
    public function getBaseClientSetup(Mage_Sales_Model_Order $order)
    {
        $client      = new Zend_Http_Client();
        $credentials = Mage::helper('kproject_sas/transaction')->getCredentials($order->getStoreId());
        $uri = $this->getPixelUrl() . '?merchantId=' . $credentials->getMerchantId();

        return $client->setUri($uri);
    }

    /**
     * Hardcoded for now, just prepping for
     * when it becomes part of the config.
     *
     * @return string
     */
    public function getPixelUrl()
    {
        return 'https://shareasale.com/sale.cfm';
    }
}
