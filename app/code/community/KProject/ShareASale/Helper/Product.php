<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
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
