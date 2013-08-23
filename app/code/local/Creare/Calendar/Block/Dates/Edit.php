<?php

class Creare_Calendar_Block_Dates_Edit extends Mage_Core_Block_Template
{
	public function getCalendarDate()
	{
		return Mage::getModel('calendar/date')->load(Mage::app()->getRequest()->getParam('id'));	
	}
	
	public function getTitle($date)
	{
		if ($date->getId())
		{
			return $this->__('Edit %s', $date->getEventName());
		} else {
			return $this->__('New Date');	
		}
	}
}