<?php

class Creare_Calendar_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function reminder($n)
	{
			$weeks = $n/7;
				
			if (is_int($weeks))
			{
				if ($weeks > 1)
				{
					return $weeks.Mage::helper('calendar')->__(' Weeks');
				} else {
					return $weeks.Mage::helper('calendar')->__(' Week');
				}
			}
			
			switch ($n)
			{
				default:
				if ($n == 1)
				{
					return $n.Mage::helper('calendar')->__(' Day');
				} else {
					return $n.Mage::helper('calendar')->__(' Days');
				}
				break;
					
			}
	}
	
	public function yearly($n)
	{
		return ($n) ? Mage::helper('calendar')->__('Yes') : Mage::helper('calendar')->__('No');
	}
	
	public function dateFormat($date, $n)
	{
		$date = strtotime($date);
		return $n == 1 ? date('jS F', $date) : date('jS F Y', $date);
	}
	
	public function reminderDate($n, $date)
	{
		return date('Y-m-d',(strtotime ( '-'.$n.' day' , strtotime ( $date) ) ));	
	}
}