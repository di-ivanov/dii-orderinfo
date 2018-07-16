<?php
/**
 * @author    Dimitar Ivanov
 * @category  Dii
 * @package   Dii_Orderinfo
 * @version   Magento 1.9.3.8
 * @copyright Dimitar Ivanov 2018
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dii_Orderinfo_Model_System_Config_Source_Mode extends Mage_Core_Model_Abstract
{
    const CONFIGURATION_MODE_PRODUCTION  = 1;
    const CONFIGURATION_MODE_STAGING     = 2;
    const CONFIGURATION_MODE_DEVELOPMENT = 3;

    /**
     * Return option array for System Config Entrys
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            ''                                   => Mage::helper('orderinfo')->__('--Please Select--'),
            self::CONFIGURATION_MODE_PRODUCTION  => Mage::helper('orderinfo')->__('Production'),
            self::CONFIGURATION_MODE_STAGING     => Mage::helper('orderinfo')->__('Staging'),
            self::CONFIGURATION_MODE_DEVELOPMENT => Mage::helper('orderinfo')->__('Development')
        );
    }
}
