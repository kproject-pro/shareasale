<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table kproject_shareasale_orders(id int not null auto_increment, order_number varchar(100), primary key(id));
    insert into kproject_shareasale_orders values(1,'tablename1');
    insert into kproject_sas_orders values(2,'tablename2');
		
SQLTEXT;

$installer->run($sql);
//demo
//Mage::getModel('core/url_rewrite')->setId(null);
//demo
$installer->endSetup();
