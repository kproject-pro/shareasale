<?php
/**
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
                   ->newTable($installer->getTable('kproject_sas/orders'))
                   ->addColumn(
                       'id',
                       Varien_Db_Ddl_Table::TYPE_INTEGER,
                       null,
                       array(
                           'identity' => true,
                           'unsigned' => false,
                           'nullable' => false,
                           'primary'  => true,
                       ),
                       'Unique identifier'
                   )
                   ->addColumn(
                       'order_number',
                       Varien_Db_Ddl_Table::TYPE_INTEGER,
                       null,
                       array(),
                       'Mage Order Increment ID'
                   )
                   ->addColumn(
                       'call_date',
                       Varien_Db_Ddl_Table::TYPE_DATETIME,
                       null,
                       array(),
                       'API call date'
                   )
                   ->addColumn(
                       'api_status',
                       Varien_Db_Ddl_Table::TYPE_TINYINT,
                       null,
                       array(),
                       'Status'
                   )
                   ->addColumn(
                       'error_code',
                       Varien_Db_Ddl_Table::TYPE_SMALLINT,
                       null,
                       array(),
                       'API error code'
                   );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
