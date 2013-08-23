<?php

$installer = $this;

$installer->startSetup();
$installer->run("
    CREATE TABLE `{$installer->getTable('calendar/date')}` (
      `id` int(11) NOT NULL auto_increment,
	  `customer_id` int(11) NOT NULL,
      `event_name` varchar(255) NULL,
      `date` date default NULL,
      `yearly` int(1) NULL,
      `reminder` varchar(255) NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
