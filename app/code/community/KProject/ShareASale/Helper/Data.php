<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TOKEN          = 'kproject/share_a_sale/general/token';
    const XML_PATH_MERCHANT_ID    = 'kproject/share_a_sale/general/merchant_id';
    const XML_PATH_SECRET_KEY     = 'kproject/share_a_sale/general/secret_key';
    const XML_PATH_REFUND_COMMENT = 'kproject/share_a_sale/general/refund_comment';

    /**
     * @param null $storeId
     *
     * @return KProject_ShareASale_Credentials
     */
    public function getCredentials($storeId = null)
    {
        $credentials = new KProject_ShareASale_Credentials();
        $credentials->setToken(Mage::getStoreConfig(self::XML_PATH_TOKEN, $storeId));
        $credentials->setMerchantId(Mage::getStoreConfig(self::XML_PATH_MERCHANT_ID, $storeId));
        $credentials->setSecretKey(Mage::getStoreConfig(self::XML_PATH_SECRET_KEY, $storeId));

        return $credentials;
    }
}
