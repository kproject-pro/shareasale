<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Block_Pixel extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function printPixel()
    {
        $img   = '';
        $order = Mage::getSingleton('checkout/session')->getLastRealOrder();

        if (!$order->getId()) {
            $orderId = Mage::getSingleton('checkout/type_onepage')->getLastOrderId();
            $order   = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        }

        $params = Mage::helper('kproject_sas/transaction')->getNewTransactionParams($order);
        try {
            $client = $this->getBaseClientSetup($order);
            $client->getUri()->addReplaceQueryParameters($params);
            $link = $client->getUri(true);
            $img  = $this->getImageHtml($link);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        return $img;
    }

    /**
     * Creates the pixel html <img> wrapper
     *
     * @param string $uri
     *
     * @return string
     */
    private function getImageHtml($uri)
    {
        /** @noinspection HtmlUnknownTarget */
        return $this->__('<img src="%s" width="1" height="1"/>', $uri);
    }

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
        //todo-konstantin: ask about discrepancy of merchantId vs merchantID
        $uri         = $this->getBaseUri() . '?merchantID=' . $credentials->getMerchantId();

        return $client->setUri($uri);
    }

    /**
     * Returns pixel main URL
     *
     * @return string
     */
    protected function getBaseUri()
    {
        return 'https://shareasale123.com/sale.cfm'; //todo-konstantin: change for live work
    }
}
