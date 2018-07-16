<?php
/**
 * @author    Dimitar Ivanov
 * @category  Dii
 * @package   Dii_Orderinfo
 * @version   Magento 1.9.3.8
 * @copyright Dimitar Ivanov 2018
 * @license   http=>//opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dii_Orderinfo_Model_Api extends Varien_Object
{
    /**
     * Notify API for Order status change
     * @param  array $apiData
     * @return bool
     */
    public function notify(array $apiData)
    {
        $url = Mage::helper('orderinfo')->getApiUrlConfig();
        if (!$url) {
            // Good place to throw Mage_Core_Exception when a full log system is in place
            return false;
        }

        $client = new Zend_Http_Client($url);
        // Put here some fancy request options if you wish
        $client->setMethod(Zend_Http_Client::PUT);
        $client->setRawData(
            json_encode($apiData),
            'application/json;charset=UTF-8'
        );

        try{
            $response = $client->request();
            // Here we can check request result $response->isSuccessful() and do something accordingly
        } catch (Mage_Core_Exception $e) {
            // Someting is slightly not OK show error to user/admin;
        } catch (Exception $e) {
            // We screwed it big time you should really log this somewhere
            Mage::logException($e);
        }

        return true;
    }
}