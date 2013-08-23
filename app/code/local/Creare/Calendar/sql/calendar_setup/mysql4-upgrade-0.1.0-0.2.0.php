<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();
$connection->addColumn($installer->getTable('calendar/date'), 'reminder_date', 'date NULL');
$installer->run("
    CREATE TABLE `creare_reminder_queue` (
      `id` int(11) NOT NULL auto_increment,
	  `date_id` int(11) NOT NULL,
      `sent` int(11) NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
