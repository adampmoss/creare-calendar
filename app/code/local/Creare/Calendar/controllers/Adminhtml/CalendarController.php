<?php

class Creare_Calendar_Adminhtml_CalendarController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    { 
		$this->_title($this->__('Calendar'))->_title($this->__('Dates'));
		$this->loadLayout();	
		$this->_setActiveMenu('customer/calendar');
		$this->renderLayout();
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('calendar/date');
        if ($id) {
            $model->load((int) $id);
            
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('This date does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('date_data', $model);
 
 	    $this->_title($this->__('calendar'))->_title($this->__('Edit Date'));
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true); 
		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		$this->renderLayout();
    }
 
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('calendar/date');
            $id = $this->getRequest()->getParam('id');
			
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                        $data[$key] = implode(',',$this->getRequest()->getParam($key));	
                }
            }			
			
            if ($id) {
                $model->load($id);
            }
			$data['reminder_date'] = Mage::helper('calendar')->reminderDate($data['reminder'], $data['date']);
			
			$model->setData($data);
			
			
			if (!$data['yearly'])
			{
					$model->setYearly(0);
			};
 
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();
 
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('calendar')->__('Error saving date'));
                }
 
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('calendar')->__('Date was successfully saved.'));
								
                Mage::getSingleton('adminhtml/session')->setFormData(false);
 
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
 
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }
 
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('No data found to save'));
        $this->_redirect('*/*/');
    }
 
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('calendar/date');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('calendar')->__('The date has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('calendar')->__('Unable to find the block to delete.'));
        $this->_redirect('*/*/');
    }	
	
}