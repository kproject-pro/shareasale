<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Model_Cron
{
    /**
     * Attempt to send previously failed transactions
     * that are in status pending & specific error_code
     */
    public function submitFailedTransactions()
    {
        if (!Mage::helper('kproject_sas')->isEnabled()) {
            return $this;
        }

        //todo: search for failed transactions, attempt to send them.
        //todo: also check if those transactions belong to a disabled store
        return $this;
    }
}
