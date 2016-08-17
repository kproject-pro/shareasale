<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED         = 'kproject_sas/general/enabled';
    const XML_PATH_MERCHANT_ID     = 'kproject_sas/general/merchant_id';
    const XML_PATH_TOKEN           = 'kproject_sas/api/token';
    const XML_PATH_SECRET_KEY      = 'kproject_sas/api/secret_key';
    const XML_PATH_API_NEW_ENABLED = 'kproject_sas/api_new/enabled';
    const XML_PATH_AFFILIATE_KEY   = 'kproject_sas/api_new/affiliate_key';
    const XML_PATH_CLICK_KEY       = 'kproject_sas/api_new/click_key';

    /**
     * @param null $storeId
     *
     * @return KProject_ShareASale_Credentials
     */
    public function getCredentials($storeId = null)
    {
        $credentials = new KProject_ShareASale_Credentials();
        $credentials->setMerchantId(Mage::getStoreConfig(self::XML_PATH_MERCHANT_ID, $storeId));

        $eToken = Mage::getStoreConfig(self::XML_PATH_TOKEN, $storeId);
        $token  = $this->getCoreHelper()->decrypt($eToken);
        $credentials->setToken($token);

        $eSKey = Mage::getStoreConfig(self::XML_PATH_SECRET_KEY, $storeId);
        $key   = $this->getCoreHelper()->decrypt($eSKey);
        $credentials->setSecretKey($key);

        return $credentials;
    }

    /**
     * Just checks if plugin is enabled via config
     *
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * Check if the new transactions are allowed
     * to be made via API
     *
     * @param null $storeId
     *
     * @return bool
     */
    public function newTransactionViaApiEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_API_NEW_ENABLED, $storeId);
    }

    /**
     * Checks if the library is accessible
     * and plugin is enabled
     *
     * @param null $storeId
     *
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return class_exists('KProject_ShareASale_Api') && $this->isEnabled($storeId);
    }

    /**
     * @return string
     */
    public function getAffiliateIdentifierKey($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_AFFILIATE_KEY, $storeId);
    }

    /**
     * @return string
     */
    public function getClickIdentifierKey($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CLICK_KEY, $storeId);
    }

    /**
     * @return KProject_ShareASale_Helper_Status
     */
    protected function statusHelper()
    {
        return Mage::helper('kproject_sas/status');
    }

    /**
     * @return Mage_Core_Helper_Abstract|Mage_Core_Helper_Data
     */
    protected function getCoreHelper()
    {
        return Mage::helper('core');
    }
}
