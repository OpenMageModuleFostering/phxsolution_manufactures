<?php
/*
/**
 * PHXSolution Manufactures Commission Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *
 * @category   Manufactures Block
 * @package    Phxsolution_Manufactures
 * @author     Prakash Vaniya 
 * @contact    contact@phxsolution.com 
 * @site       www.phxsolution.com 
 * @copyright  Copyright (c) 2014 PHXSolution Manufactures
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Manufactures_Block_Adminhtml_Manufactures_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	  parent::__construct();
	  $this->setId('manufacturesGrid');
	  $this->setDefaultSort('manufactures_id');
	  $this->setDefaultDir('ASC');
	  $this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
	{
	  $collection = Mage::getModel('manufactures/manufactures')->getCollection();
	  $this->setCollection($collection);
	  return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
	  $this->addColumn('manufactures_id', array(
		  'header'    => Mage::helper('manufactures')->__('ID'),
		  'align'     =>'right',
		  'width'     => '50px',
		  'index'     => 'manufactures_id',
	  ));
	
	  $this->addColumn('option_id', array(
		  'header'    => Mage::helper('manufactures')->__('Name'),
		  'align'     =>'left',
		  'index'     => 'option_id',
		  'type'      => 'options',
		  'options'   => Mage::getSingleton('manufactures/manufactures')->getAttributeOptions('manufacturer'),
	  ));

		
		$this->addColumn('image', array(
			'header'    => Mage::helper('manufactures')->__('Logo'),
			'align'     =>'left',
			"width" => "80px",
			'index'     => 'image',
			'renderer'  => 'manufactures/adminhtml_manufactures_renderer_image'
		));
		$this->addColumn('note', array(
			'header' => Mage::helper('manufactures')->__('Note'),
			'index' => 'note',
		));
		
	  $this->addColumn('profit_value', array(
		  'header'    => Mage::helper('manufactures')->__('Profit Value'),
		  'align'     =>'left',
		  'width'     => '110px',
		  'index'     => 'profit_value',
		));	
	  
	  $this->addColumn('profit', array(
		  'header'    => Mage::helper('manufactures')->__('Profit In'),
		  'align'     => 'left',
		  'width'     => '110px',
		  'index'     => 'profit',
		  'type'      => 'options',
		  'options'   => Mage::getSingleton('manufactures/profit')->getOptionArray()
	  ));
	  
		$this->addColumn('action',
			array(
				'header'    =>  Mage::helper('manufactures')->__('Action'),
				'width'     => '100',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('manufactures')->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
		));
		
		//$this->addExportType('*/*/exportCsv', Mage::helper('manufactures')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('manufactures')->__('XML'));
	  
	  return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('manufactures_id');
		$this->getMassactionBlock()->setFormFieldName('manufactures');
	
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'    => Mage::helper('manufactures')->__('Delete'),
			 'url'      => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('manufactures')->__('Are you sure?')
		));
	
		$profits = Mage::getSingleton('manufactures/profit')->getOptionArray();
		
		array_unshift($profits, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('profit', array(
			 'label'=> Mage::helper('manufactures')->__('Change Profit Type'),
			 'url'  => $this->getUrl('*/*/massProfit', array('_current'=>true)),
			 'additional' => array(
					'visibility' => array(
						 'name' => 'profit',
						 'type' => 'select',
						 'class' => 'required-entry',
						 'label' => Mage::helper('manufactures')->__('Profit'),
						 'values' => $profits
					 )
			 )
		));
		return $this;
	}
	
	public function getRowUrl($row)
	{
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}