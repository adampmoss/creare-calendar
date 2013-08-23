<?php
 
class Creare_Calendar_Block_Adminhtml_Date_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('date_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('calendar/date')->getCollection();
		
		$customertable = Mage::getSingleton('core/resource')->getTableName('customer/entity');
		
		$collection->getSelect()->join( array('customer' => $customertable), 'main_table.customer_id = customer.entity_id', array('customer.*'));
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('calendar')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id'
        ));
		
		$this->addColumn('customer_id', array(
            'header'    => Mage::helper('calendar')->__('Customer ID'),
            'align'     =>'right',
            'width'     => '70px',
            'index'     => 'customer_id'
        ));
		
		$this->addColumn('email', array(
            'header'    => Mage::helper('calendar')->__('Customer Email'),
            'index'     => 'email'
        ));
		
		$this->addColumn('event_name', array(
            'header'    => Mage::helper('calendar')->__('Event Name'),
            'index'     => 'event_name'
        ));
		
		$this->addColumn('date', array(
            'header'    => Mage::helper('calendar')->__('Date'),
            'index'     => 'date',
			'type'      => 'date'
        ));
 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}