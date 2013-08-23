<?php

class Creare_Calendar_Block_Adminhtml_Date extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'calendar';
        $this->_controller = 'adminhtml_date';
        $this->_headerText = Mage::helper('calendar')->__('Dates'); 
        parent::__construct();
    }
}
