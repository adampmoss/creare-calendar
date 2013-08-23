<?php

class Creare_Calendar_DatesController extends Mage_Core_Controller_Front_Action
{
	
	public function indexAction()
	{
		$this->loggedInOnly();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$this->loggedInOnly();
		
		if ($id = $this->getRequest()->getParam('id'))
		{
			$date = Mage::getModel('calendar/date')->load($id);
			
			if ($date->getCustomerId() != Mage::getSingleton('customer/session')->getId()) 
			{
					Mage::getSingleton('core/session')->addError(Mage::helper('calendar')->__('Customer ID does not match'));
					return $this->_redirect('*/*');	
			}
		}
			
		$this->_title($this->__('Edit Date'));
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		$this->loggedInOnly();
		
		if ($data = $this->getRequest()->getPost())
		{
			
			$id = $this->getRequest()->getParam('id');
			$data['customer_id'] = Mage::getSingleton('customer/session')->getId();
			$model = Mage::getModel('calendar/date');
			
			if (!$data['yearly'])
			{
				$data['yearly'] = 0;
			};
			
			$data['reminder_date'] = Mage::helper('calendar')->reminderDate($data['reminder'], $data['date']);
			
			foreach ($data as $key => $value)
			{
				if (is_array($value))
				{
						$data[$key] = implode(',',$this->getRequest()->getParam($key));	
				}
			}	
			
			
			try {
				
				if ($id)
				{
					$model->load($id);
					if ($model->getCustomerId() != $data['customer_id']) 
					{
						Mage::throwException(Mage::helper('calendar')->__('You are not permitted to edit this date'));
					}
					$model->addData($data);
					$model->setReminderDate($data['reminder_date']);
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('calendar')
					->__('Date was successfully updated'));
					
				} else {
					$model->setData($data);
					$model->setReminderDate($data['reminder_date']);
					Mage::getSingleton('core/session')->addSuccess(Mage::helper('calendar')
					->__('Date was successfully saved'));
				}
				
				$model->save();
				$this->_redirect('*/*/');
			
			} catch (Exception $e) {
				
				Mage::getSingleton('core/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
				
			}
				
			return;	
		}
		
		Mage::getSingleton('core/session')->addError(Mage::helper('calendar')->__('No data to save'));
		$this->_redirect('*/*/');
		
	}
	
	public function deleteAction()
	{
		$this->loggedInOnly();
		
		$id = $this->getRequest()->getParam('id');
		$customerid = Mage::getSingleton('customer/session')->getId();
		$model = Mage::getModel('calendar/date');
		
		try {
				
			if ($id)
			{
				$model->load($id);
				
				if ($model->getCustomerId() != $customerid) 
				{
					Mage::throwException(Mage::helper('calendar')->__('You are not permitted to delete this date'));
				}
				
				$model->delete();
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('calendar')->__('Date deleted'));
				$this->_redirect('*/*/');	
			} else {
				Mage::throwException(Mage::helper('calendar')->__('No date ID specified'));
			}
			
		} catch (Exception $e) {
				
			Mage::getSingleton('core/session')->addError($e->getMessage());	
			$this->_redirect('*/*/');
				
		}
		
	}
	
	public function loggedInOnly()
	{
		if (!Mage::getSingleton('customer/session')->getId())
		{
			return $this->_redirect('customer/account');
		}
	}
	
	
	/*********** Cron Job - to be mved to Model *****************/
	
	/*const XML_PATH_REMINDER_EMAIL = 'calendar/dates/email_template';

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
		
		// Send Transactional Email
		Mage::getModel('core/email_template')
			->sendTransactional($template, $sender, $recepientEmail, $recepientName, $vars, $storeId);
			
		$translate->setTranslateInline(true);
	
	}
	
	
	public function testAction($templateConfigPath = self::XML_PATH_REMINDER_EMAIL)
	{
		$storeId = Mage::app()->getStore()->getStoreId();
		$template = Mage::getStoreConfig($templateConfigPath, $storeId);
		$conn = Mage::getSingleton('core/resource');
		$read = $conn->getConnection('core_read');
		$write = $conn->getConnection('core_write');
		$model = Mage::getModel('calendar/date');
		
		$deletearray = array();
		
		$result = $read->fetchAll('SELECT date_id FROM creare_reminder_queue LIMIT 50');
		
		foreach ($result as $date)
		{
			$this->_createEmail($storeId, $template, $model->load($date['date_id']));
			$deletearray[] = $date['date_id'];
		}
		
		$write->query('DELETE FROM creare_reminder_queue WHERE date_id IN ('.implode(',',$deletearray).')');
		
	}
	
	public function getDatesCollection()
	{
		return Mage::getModel('calendar/date')->getCollection();
	}
	
	public function dateCheck($date)
	{
		$now = time();
		$your_date = strtotime($date->getDate());
		
		if ($date->getYearly())
		{
			$your_date = strtotime( date( 'd M ', $your_date ) . date( 'Y' ) ) ;
			//echo date('d M Y', $your_date);
			
		} else {
		
			$your_date = strtotime($date->getDate());
			//echo date('d M Y', $your_date);
		}
		
		$datediff = ceil(($your_date - $now)/(60*60*24));
		echo $date->getEventName().': '.$datediff.'<br />';
		
		if ($datediff == $date->getReminder())
		{
			return true;	
		}
	}*/
	
	public function testingAction()
	{
		$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
		$dates = Mage::getModel('calendar/date')->getCollection();
		$dateids = array();
		
		foreach ($dates as $date)
		{
			if (strtotime($date->getReminderDate()) == strtotime(date('d M Y')))
			{
				$dateids[] = '('.$date->getId().')';
			}
		}
		
		echo implode(',',$dateids);
		
		$conn->query('INSERT INTO creare_reminder_queue (date_id) VALUES '.implode(',',$dateids).'');
		
	}
	
}