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
        $uri         = $this->getPixelUrl() . '?merchantId=' . $credentials->getMerchantId();

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
