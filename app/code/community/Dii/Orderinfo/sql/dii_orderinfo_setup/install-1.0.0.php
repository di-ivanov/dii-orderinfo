<?php
/**
 * @author    Dimitar Ivanov
 * @category  Dii
 * @package   Dii_Orderinfo
 * @version   Magento 1.9.3.8
 * @copyright Dimitar Ivanov 2018
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

// Let's save some writting by defining attributes first
$attributes = array(
    'api_sync' => array(
        'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment'  => 'Is Synced with API'
    ),
    'api_sync_time' => array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'comment'  => 'Time when sync with API occured'
    ),
);

foreach ($attributes as $name => $options) {
    // Here we can loop tables too, but it gets a little bit too much for an install script
    $installer->getConnection()
              ->addColumn(
                    $installer->getTable('sales/order'),
                    $name,
                    $options
              );

    $installer->getConnection()
              ->addColumn(
                    $installer->getTable('sales/order_grid'),
                    $name,
                    $options
              );
}

$installer->endSetup();
