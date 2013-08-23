<?php class Creare_Calendar_Model_Observer
{
	public function _createEmail($storeId, $template, $date)
	{
		$customer = Mage::getModel('customer/customer')->load($date->getCustomerId());
		
		$vars = array(
			'name' => $customer->getName(),
			'event' => $date->getEventName(),
			'date' => Mage::helper('calendar')->dateFormat($date->getDate(), $date->getYearly()),
			'reminder' => Mage::helper('calendar')->reminder($date->getReminder())
			);
		
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		$sender = array('name' => $senderName, 'email' => $senderEmail);
		$recepientEmail = $customer->getEmail();
		$recepientName = $customer->getName();
		
		$translate  = Mage::getSingleton('core/translate');
		
		try {
			// Send Transactional Email
			Mage::getModel('core/email_template')
			->sendTransactional($template, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			
		} catch (Exception $e)
		{
			Mage::log($e->getMessage());
			return;
		}
		$translate->setTranslateInline(true);
		return;
	
	}
	
	public function sendEmails($templateConfigPath = self::XML_PATH_REMINDER_EMAIL)
	{
		$storeId = Mage::app()->getStore()->getStoreId();
		$template = Mage::getStoreConfig('calendar/dates/email_template');
		$conn = Mage::getSingleton('core/resource');
		$read = $conn->getConnection('core_read');
		$write = $conn->getConnection('core_write');
		$model = Mage::getModel('calendar/date');
		
		$deletearray = array();
		
		$result = $read->fetchAll('SELECT * FROM creare_reminder_queue LIMIT 50');
		
		foreach ($result as $date)
		{
			$this->_createEmail($storeId, $template, $model->load($date['date_id']));
			$deletearray[] = $date['id'];
		}
		
		$write->query('DELETE FROM creare_reminder_queue WHERE id IN ('.implode(',',$deletearray).')');
		
		return;
	}
	
	
	public function getDatesCollection()
	{
		return Mage::getModel('calendar/date')->getCollection();
	}
	
	public function refreshDates()
	{
		
		$datescollection = Mage::getModel('calendar/date')->getCollection()->addFieldToFilter('yearly', 1);
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		$dates = Mage::getModel('calendar/date')->getCollection();
		$dateids = array();
		
		foreach ($datescollection as $date)
		{
			if (date('Y', strtotime($date->getReminderDate())) != date('Y'))
			{
				$date->setReminderDate(date( date( 'Y' ).'-M-d', strtotime($date->getReminderDate()) ) ) ;
				$date->save();
			}
		}
		
		foreach ($dates as $date)
		{
			if (strtotime($date->getReminderDate()) == strtotime(date('d M Y')))
			{
				$dateids[] = '('.$date->getId().')';	
			}
		}
		
		
		$conn->query('INSERT INTO creare_reminder_queue (date_id) VALUES '.implode(',',$dateids).'');
		
		mail("adam@creare.co.uk", "Refresh","Moved the following dates into schedule: ".var_export($dateids, true)); 
		
		return;
		
	}

}