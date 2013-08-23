<?php
 
class Creare_Calendar_Block_Adminhtml_Date_Edit_Tab_Date extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if (Mage::registry('date_data'))
        {
            $data = Mage::registry('date_data')->getData();
        }
        else
        {
            $data = array();
        }
 
        $form = new Varien_Data_Form();
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('dateinfo', array(
             'legend' =>Mage::helper('calendar')->__('Date Information')
        ));
		
		if (empty($data)) {
			$fieldset->addField('customer_id', 'text', array(
             'label'     => Mage::helper('calendar')->__('Customer ID'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'customer_id',
			 'style'	 => 'width:50px'
        	));
		}
		
		$fieldset->addField('event_name', 'text', array(
             'label'     => Mage::helper('calendar')->__('Event Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'event_name'
        ));
		
		$fieldset->addField('date', 'date', array(
             'label'     => Mage::helper('calendar')->__('Date'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'date',
			 'format'    => 'yyyy-MM-dd',
        	 'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        ));
		
		$fieldset->addField('yearly', 'checkbox', array(
             'label'     => Mage::helper('calendar')->__('Yearly Event?'),
          	 'after_element_html' => '<small>Does this event occur every year?</small>',
             'required'  => false,
             'name'      => 'yearly',
             'checked'	 => $data["yearly"],
    		 'onclick'   => 'this.value = this.checked ? 1 : 0;',
        ));
 
 		$fieldset->addField('reminder', 'text', array(
             'label'     => Mage::helper('calendar')->__('Remind me'),
             'class'     => 'required-entry',
             'required'  => true,
          	 'after_element_html' => 'days before the event date.',
             'name'      => 'reminder',
        ));
		
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}