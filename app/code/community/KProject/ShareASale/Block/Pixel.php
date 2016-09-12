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
        $img = '';
        if (!Mage::helper('kproject_sas')->isEnabled()) {
            return $img;
        }

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
