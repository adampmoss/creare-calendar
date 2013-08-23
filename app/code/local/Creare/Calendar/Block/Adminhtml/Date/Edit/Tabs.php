<?php
 
class Creare_Calendar_Block_Adminhtml_Date_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	  {
		  parent::__construct();
		  $this->setId('date_tabs');
		  $this->setDestElementId('edit_form');
		  $this->setTitle(Mage::helper('calendar')->__('Date Information'));
	  }
 
  protected function _beforeToHtml()
  {
      $this->addTab('dateinfo', array(
          'label'     => Mage::helper('calendar')->__('Info'),
          'title'     => Mage::helper('calendar')->__('Info'),
          'content'   => $this->getLayout()->createBlock('calendar/adminhtml_date_edit_tab_date')->toHtml()
      ));
      
      return parent::_beforeToHtml();
  }
}