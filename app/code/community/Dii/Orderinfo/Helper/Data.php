<?php
/**
 * @author    Dimitar Ivanov
 * @category  Dii
 * @package   Dii_Orderinfo
 * @version   Magento 1.9.3.8
 * @copyright Dimitar Ivanov 2018
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dii_Orderinfo_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_MODULE_ENABLED = 'orderinfo_options/configuration/enabled';
    const XML_PATH_MODE           = 'orderinfo_options/configuration/mode';
    const XML_PATH_PRODUCTION_API = 'orderinfo_options/configuration/production_api';
    const XML_PATH_STAGING_API    = 'orderinfo_options/configuration/staging_api';
    const XML_PATH_DVELOPMENT_API = 'orderinfo_options/configuration/development_api';

    /**
     * Check if module is enabled from its System Config options
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_MODULE_ENABLED);
    }

    /**
     * Fetch API Url from config dependent of Mode
     * @return mixed bool | string
     */
    public function getApiUrlConfig()
    {
        $mode = Mage::getStoreConfig(self::XML_PATH_MODE);
        if (!$mode) {
            return false;
        }

        /**
         * This will be better as a const but PHP should be 5.6 and up
         * so let's go for compatibility
         * @var array $modeFromXml => $xmlPathToFetchUrl
         */
        $modeToUrl = array(
            Dii_Orderinfo_Model_System_Config_Source_Mode::CONFIGURATION_MODE_PRODUCTION  => self::XML_PATH_PRODUCTION_API,
            Dii_Orderinfo_Model_System_Config_Source_Mode::CONFIGURATION_MODE_STAGING     => self::XML_PATH_STAGING_API,
            Dii_Orderinfo_Model_System_Config_Source_Mode::CONFIGURATION_MODE_DEVELOPMENT => self::XML_PATH_DVELOPMENT_API
        );

        if (!array_key_exists($mode, $modeToUrl)) {
            return false;
        }

        return Mage::getStoreConfig($modeToUrl[$mode]);
    }

    /**
     * Return array of arguments that will define column api_sync in Sales Order Grid
     * The same ones that are usually passed in Grid::_prepareColumns()
     * @return array
     */
    public function getApiSyncOrderGridArguments()
    {
        return array(
            'header'  => $this->__('API Sync'),
            'index'   => 'api_sync',
            'type'    => 'options',
            'width'   => 80,
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')
                ->toArray()
        );
    }

    /**
     * Return array of arguments that will define column api_sync_time in Sales Order Grid
     * The same ones that are usually passed in Grid::_prepareColumns()
     * @return array
     */
    public function getApiSyncTimeOrderGridArguments()
    {
        return array(
            'header' => $this->__('API Sync Time'),
            'index'  => 'api_sync_time',
            'type'   => 'datetime',
            'width'  => 100
        );
    }
}
