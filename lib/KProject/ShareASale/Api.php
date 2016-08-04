<?php

/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class KProject_ShareASale_Api
{

    const API_VERSION = 2.8;

    /** @var KProject_ShareASale_Credentials */
    private $credentials;

    public function __construct(KProject_ShareASale_Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    public function createNewTransaction(Mage_Sales_Model_Order $order)
    {
        $action     = 'new';
        $trans_type = 'sale';
        $client     = new Zend_Http_Client();
        $uri        =
            "https://api.shareasale.com/w.cfm" .
            "?merchantId=" . $this->credentials->getMerchantId() .
            "&token=" . $this->credentials->getToken() .
            "&version=" . self::API_VERSION .
            "&action=" . $action .
            '&transtype=' . $trans_type .
            '&userID=' . $order->getData('sharesale_user_id') .
            '&tracking=' . $order->getIncrementId() .
            '&sscid=' . $order->getData('sscid');
        $response   = $client
            ->setHeaders('x-ShareASale-Date', $this->getTimeStamp())
            ->setHeaders('x-ShareASale-Authentication', $this->getSignature($action))
            ->setMethod()
            ->setUri($uri)
            ->request();
    }

    private function getAuthenticationToken()
    {
        $APIVersion   = self::API_VERSION;
        $myMerchantID = $this->credentials->getMerchantId(); //'53899';
        $APIToken     = $this->credentials->getToken();
        $myTimeStamp  = $this->getTimeStamp();

        $actionVerb = 'bannerList'; //new?
        $sigHash    = $this->getSignature($actionVerb);

        $myHeaders = array("x-ShareASale-Date: $myTimeStamp", "x-ShareASale-Authentication: $sigHash");

        $ch  = curl_init();
        $uri =
            "https://api.shareasale.com/w.cfm?merchantId=$myMerchantID&token=$APIToken&version=$APIVersion&action=$actionVerb";

        curl_setopt(
            $ch, CURLOPT_URL,
            $uri
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $myHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $returnResult = curl_exec($ch);

        if ($returnResult) {
            //parse HTTP Body to determine result of request
            if (stripos($returnResult, 'Error Code ')) {
                // error occurred
                trigger_error($returnResult, E_USER_ERROR);
            } else {
                // success
                echo $returnResult;
            }
        } else {
            // connection error
            trigger_error(curl_error($ch), E_USER_ERROR);
        }

        curl_close($ch);
    }

    /**
     * Returns the signature to pass in the
     * header for authentication purposes
     *
     * @param $actionVerb
     *
     * @return string
     */
    public function getSignature($actionVerb)
    {
        $APIToken     = $this->credentials->getToken();
        $APISecretKey = $this->credentials->getSecretKey();
        $sig          = $APIToken . ':' . $this->getTimeStamp() . ':' . $actionVerb . ':' . $APISecretKey;

        return hash('sha256', $sig);
    }

    /**
     * @return string
     */
    public function getTimeStamp()
    {
        return gmdate(DATE_RFC1123);
    }
}
