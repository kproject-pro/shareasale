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
class KProject_ShareASale_Credentials
{
    /** @var string $token */
    protected $token;
    /** @var string $merchantId */
    protected $merchantId;
    /** @var string $secretKey */
    protected $secretKey;

    /**
     * @param string $token
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $merchantId
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string $secretKey
     *
     * @return KProject_ShareASale_Credentials
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'token'      => $this->getToken(),
            'merchantID' => $this->getMerchantId(),
            'secret_key' => $this->getSecretKey()
        );
    }
}
