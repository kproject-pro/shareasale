<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Block_Checkout_Pixel extends Mage_Core_Block_Abstract
{
    /**
     * @return string
     */
    public function printPixel()
    {
        $order  = Mage::getSingleton('checkout/session')->getLastRealOrder();
        $params = Mage::helper('kproject_sas/transaction')->getNewTransactionParams($order);

        $client = new Zend_Http_Client();
        $client->setUri($this->getBaseUri());
        $client->getUri()->addReplaceQueryParameters($params);
        $link = $client->getUri(true);

        return $this->getImageHtml($link);
    }

    private function getBaseUri()
    {
        return 'https://shareasale.com/sale.cfm'; //todo-konstantin: get the correct creds
    }

    /**
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
