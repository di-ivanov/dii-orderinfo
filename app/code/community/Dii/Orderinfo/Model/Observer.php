<?php
/**
 * @author    Dimitar Ivanov
 * @category  Dii
 * @package   Dii_Orderinfo
 * @version   Magento 1.9.3.8
 * @copyright Dimitar Ivanov 2018
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dii_Orderinfo_Model_Observer
{
    /**
     * After each save of order check for status change
     * and Notify API
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderSaveAfterApiNotify(Varien_Event_Observer $observer)
    {
        /* @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();
        // Check if order status has changed and we have work to do
        if ($order->getOrigData('status') !== $order->getData('status')
            && Mage::helper('orderinfo')->isEnabled()) {
            try {
                $apiData = array(
                    'order_id'   => $order->getIncrementId(),
                    'order_data' => array(
                        'order_status'    => $order->getStatus(),
                        'payment_method'  => $order->getPayment()->getMethodInstance()->getTitle(),
                        // This will give you total quantity from all products
                        // if you are intereseted in the number of unique products
                        // getTotalItemCount()
                        'number_of_items' => $order->getTotalQtyOrdered(),
                        'sub_total'       => $order->getBaseSubtotal(),
                        'discount'        => $order->getBaseDiscountAmount(),
                        'grand_total'     => $order->getBaseGrandTotal()
                    ),
                    'customer_data' => array(
                        'customer_id' => $order->getCustomerId(),
                        'firstname'   => $order->getCustomerFirstname(),
                        'lastname'    => $order->getCustomerLastname(),
                        'address'     => '',
                        'city'        => '',
                        'country'     => '',
                        'email'       => $order->getCustomerEmail()
                    )
                );

                // Which Address do you want?
                // Let's make waterfall some e-shops remove addres req to finish order
                $address = false;
                if (($shippingAddress = $order->getShippingAddress()) instanceof Mage_Sales_Model_Order_Address) {
                    $address = $shippingAddress;
                } else if (($billingAddress = $order->getBillingAddress()) instanceof Mage_Sales_Model_Order_Address) {
                    $address = $billingAddress;
                } else {
                    // If you are desperate enough try this, but I wont implement it
                    // $order->getAddressesCollection() and Do smth
                }

                if ($address) {
                    $countryId = $address->getCountryId();
                    $country   = Mage::getModel('directory/country')->load($countryId)
                                                                    ->getName();

                    $apiData['customer_data']['address'] = implode(' ',  $address->getStreet());
                    $apiData['customer_data']['city']    = $address->getCity();
                    $apiData['customer_data']['country'] = $country;
                }

                $notifyApi = Mage::getModel('orderinfo/api')->notify($apiData);

                $order->setApiSync($notifyApi)
                      ->setApiSyncTime(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));

                $order->getResource()
                      ->saveAttribute($order, 'api_sync')
                      ->saveAttribute($order, 'api_sync_time');
            } catch(Exception $e) {
                // We screwed it big time you should really log this somewhere
                Mage::logException($e);
            }
        }
    }
}
