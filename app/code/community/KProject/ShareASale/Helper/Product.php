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
class KProject_ShareASale_Helper_Product extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function getItemParams(Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Sales_Model_Order_Item[] $items */
        $items   = $order->getAllVisibleItems();
        $skuList = $quantityList = $priceList = '';

        $last_index = array_search(end($items), $items, true);
        foreach ($items as $index => $item) {
            $delimiter = $index === $last_index ? '' : ',';
            $skuList .= $item->getSku() . $delimiter;
            $quantityList .= ceil($item->getQtyOrdered()) . $delimiter;
            $priceList .= ($item->getProduct()->getFinalPrice() - ($item->getDiscountAmount() / $item->getQtyOrdered()))
                . $delimiter;
        }

        return array(
            'skulist'      => $skuList,
            'pricelist'    => $priceList,
            'quantitylist' => $quantityList
        );
    }
}
