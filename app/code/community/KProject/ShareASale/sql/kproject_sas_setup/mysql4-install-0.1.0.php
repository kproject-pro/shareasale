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
                        'parameters',
                        Varien_Db_Ddl_Table::TYPE_TEXT,
                        1000,
                        array(),
                        'Parameters passed'
                   )
                   ->addColumn(
                       'api_status',
                       Varien_Db_Ddl_Table::TYPE_TINYINT,
                       2,
                       array(),
                       'Status'
                   )
                   ->addColumn(
                       'error_code',
                       Varien_Db_Ddl_Table::TYPE_SMALLINT,
                       null,
                       array(),
                       'API error code'
                   )
                    ->addColumn(
                        'retry_count',
                        Varien_Db_Ddl_Table::TYPE_TINYINT,
                        3,
                        array(
                            'default' => 0
                        ),
                        '# of resend attempts'
                    );

if (!$installer->getConnection()->isTableExists($table->getName())) {
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
