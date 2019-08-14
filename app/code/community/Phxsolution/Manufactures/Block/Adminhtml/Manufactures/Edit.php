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
class Phxsolution_Manufactures_Block_Adminhtml_Manufactures_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'manufactures';
        $this->_controller = 'adminhtml_manufactures';
        
        $this->_updateButton('save', 'label', Mage::helper('manufactures')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('manufactures')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('manufactures_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'manufactures_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'manufactures_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        $manufacturesModel = Mage::getModel('manufactures/manufactures')->load($this->getRequest()->getParam('id'));
        $manufacturesId = $manufacturesModel->getOptionId();
        $manufacturesLabel = Mage::helper('manufactures')->getManufacturesLabel($manufacturesId);

        if( Mage::registry('manufactures_data') && Mage::registry('manufactures_data')->getId() ) {
            return Mage::helper('manufactures')->__("Edit Manufacturer '%s'", $manufacturesLabel);
        } else {
            return Mage::helper('manufactures')->__('Add Manufacturer');
        }
    }
}