<?php

class Creare_Calendar_Block_Dates extends Mage_Core_Block_Template
{
	
	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();	
	}
	
	public function getDates()
	{
		return Mage::getModel('calendar/date')->getCollection()
					->addFieldToFilter('customer_id', array('eq'=> $this->getCustomer()->getId()));
	}
	
}