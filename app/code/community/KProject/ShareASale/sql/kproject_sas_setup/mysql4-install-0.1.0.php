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
 *
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
