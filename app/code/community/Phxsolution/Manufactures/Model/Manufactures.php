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
 * @category   Manufactures Model
 * @package    Phxsolution_Manufactures
 * @author     Prakash Vaniya 
 * @contact    contact@phxsolution.com 
 * @site       www.phxsolution.com 
 * @copyright  Copyright (c) 2014 PHXSolution Manufactures
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Manufactures_Model_Manufactures extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('manufactures/manufactures');
    }
	
	static public function getAttributeInfo($attribute_code)
    {
		$attribute_details = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $attribute_code);
		return $attribute = $attribute_details->getData(); // returns array		
	}
	
	static public function getAttributeOptions($attribute_code)
    {
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
        $options = array();
		$options[""] = "Select Manufacture";
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
            if($option['value'] != ""){
				$options[$option['value']] = $option['label'];
			}
        }
        return $options;
    }
}