<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Block_Pixel extends Mage_Core_Block_Template
{
    /**
     * Returns the full pixel html
     * to print in the body of frontend
     *
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
            $client = $this->getPixelHelper()->getBaseClientSetup($order);
            $client->getUri()->addReplaceQueryParameters($params);
            $link = $client->getUri(true);
            $img  = $this->getImageHtml($link);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        return $img;
    }

    /**
     * @return KProject_ShareASale_Helper_Pixel
     */
    public function getPixelHelper()
    {
        return Mage::helper('kproject_sas/pixel');
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
}
