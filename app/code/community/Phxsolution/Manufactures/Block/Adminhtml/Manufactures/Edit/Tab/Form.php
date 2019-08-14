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
class Phxsolution_Manufactures_Block_Adminhtml_Manufactures_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('manufactures_form', array('legend'=>Mage::helper('manufactures')->__('Manufacturer information')));
		
		$AttributeInfo = Mage::getSingleton('manufactures/manufactures')->getAttributeInfo('manufacturer');
		
		$fieldset->addField('attribute_id', 'hidden', array(
			'name' 	 => 'attribute_id',
			'value'  => $AttributeInfo["attribute_id"],
		));
		$form->setValues(Mage::registry('manufactures_data')->setData("attribute_id",$AttributeInfo["attribute_id"]));
		
		$fieldset->addField('option_id', 'select', array(
		  'label'     => Mage::helper('manufactures')->__('Name'),
		  'class'     => 'validate-select',
		  'required'  => true,
		  'name'      => 'option_id',
		  'values'    => Mage::getSingleton('manufactures/manufactures')->getAttributeOptions('manufacturer'),
		));
		
		$fieldset->addField('profit_value', 'text', array(
		  'label'     => Mage::helper('manufactures')->__('Profit Value'),
		  'class'     => 'required-entry validate-digits',
		  'required'  => true,
		  'name'      => 'profit_value',
		  'maxlength'      => '2',
		  
		));
		
		$fieldset->addField('profit', 'select', array(
		  'label'     => Mage::helper('manufactures')->__('Profit In'),
		  'name'      => 'profit',
		  'values'    => Mage::getSingleton('manufactures/profit')->getOptionArray(),
		));

		$fieldset->addField('image', 'image', array(
			'label' => Mage::helper('manufactures')->__('Logo'),
			'name' => 'image',
			'note' => '(*.jpg, *.png, *.gif)',
		));
		$fieldset->addField('note', 'text', array(
		  'label'     => Mage::helper('manufactures')->__('note'),
		  'required'  => false,
		  'name'      => 'note',
		));
		
		if ( Mage::getSingleton('adminhtml/session')->getManufacturesData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getManufacturesData());
		  	Mage::getSingleton('adminhtml/session')->setManufacturesData(null);
		} elseif ( Mage::registry('manufactures_data') ) {
		  	$form->setValues(Mage::registry('manufactures_data')->getData());
		}
		
		return parent::_prepareForm();
	}
}