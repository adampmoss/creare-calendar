<?php

class Creare_Calendar_Model_Resource_Date extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() 
    {
        $this->_init('calendar/date', 'id');
    }
}