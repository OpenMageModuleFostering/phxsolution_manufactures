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
class Phxsolution_Manufactures_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /* Overwritten to be able to add custom columns to the product grid. Normally
     * one would overwrite the function _prepareCollection, but it won't work because
     * you have to call parent::_prepareCollection() first to get the collection.
     *
     * But since parent::_prepareCollection() also finishes the collection, the
     * joins and attributes to select added in the overwritten _prepareCollection()
     * are 'forgotten'.
     *
     * By overwriting setCollection (which is called in parent::_prepareCollection()),
     * we are able to add the join and/or attribute select in a proper way.
     *
     */
    public function setCollection($collection)
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $store = $this->_getStore();		       
		
		// Add our custom attributes
        $collection->addAttributeToSelect('manufacturer');
		
        parent::setCollection($collection);
    }

	protected function _prepareColumns()
    {
    	if(Mage::helper('manufactures')->getIsEnabled())
    	{
	    	$store = $this->_getStore();
	        
			$this->addColumnAfter('manufacturer',
	            array(
	                'header'=> Mage::helper('catalog')->__('Manufacturer'),
	                'width' => '80px',
	                'index' => 'manufacturer',
	                'type'  => 'options',
	                'options' => $this->_getAttributeOptions('manufacturer'),
	        	),
				'sku'
			);
			
			$this->addColumnAfter('manufacturer_profit',
	            array(
	                'header'=> Mage::helper('catalog')->__('Commission'),
	                'width' => '80px',
	                'index' => 'manufacturer_profit',
	                'sortable' => false,
					'type'  => 'number',
					'frame_callback' => array($this, 'callbackProfit'),
					'filter_condition_callback' => array($this, 'callbackFilterProfit')
	        	),
				'manufacturer'
			);
			
			$this->addColumnAfter('gross_price',
	            array(
	                'header'=> Mage::helper('catalog')->__('Gross Price'),
	                'type'  => 'price',
	                'sortable' => false,
					'currency_code' => $store->getBaseCurrency()->getCode(),
	                'index' => 'gross_price',
					'frame_callback' => array($this, 'callbackGrossPrice'),
					'filter_condition_callback' => array($this, 'callbackFilterGrossPrice')
	            ),
	            'price'
	        );
		}
        return parent::_prepareColumns();
	}
	
	public function callbackProfit($value, $row, $column, $isExport){
		$mInfo =  Mage::getModel('manufactures/manufactures')->getCollection()->addFilter('option_id', $row->getManufacturer())->getData();
		$mInfo = $mInfo[0];
		
		if($mInfo["profit_value"] == NULL)
			return "";
		else{
			return $mInfo["profit_value"]. (($mInfo["profit"] == 1)?"%":" Fixed");
		}			
	}
	protected function callbackFilterProfit($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == null) {
            return;
        }
		$mcollections =  Mage::getModel('manufactures/manufactures')->getCollection();
		$mcollections->addFieldToFilter('profit_value', array('gteq' =>$value["from"]));
		$mcollections->addFieldToFilter('profit_value', array('lteq' =>$value["to"]));
		
		$productIds = array();
		foreach ($mcollections as $mcollection) {
			$productCollections = Mage::getModel('catalog/product')->getCollection();
			$productCollections->addFieldToFilter('manufacturer', array('eq' =>$mcollection->getOptionId()));
			foreach ($productCollections as $productCollection) {
				$productIds[] = $productCollection->getId();
			}
		}
		$collection->addFieldToFilter('entity_id', array('in' => $productIds));
	}
	
	public function callbackGrossPrice($value, $row, $column, $isExport){
		
		$mInfo =  Mage::getModel('manufactures/manufactures')->getCollection()->addFilter('option_id', $row->getManufacturer())->getData();
		$mInfo = $mInfo[0];
		
		if($mInfo["profit_value"] == NULL)
			if($row->getPrice() == NULL)
				return ;
			else
				return Mage::helper('core')->currency($row->getPrice(), true, false);
		else{
			$price = $row->getPrice();
			
			if($mInfo["profit"] == 1){
				$totolPrice = ($price * $mInfo["profit_value"])/100;
				$grossPrice = ($price + $totolPrice);
			}else{
				$grossPrice = ($price + $mInfo["profit_value"]);
			}
			
			return Mage::helper('core')->currency($grossPrice, true, false);
		}			
	}
	protected function callbackFilterGrossPrice($collection, $column)
    {
        $value = $column->getFilter()->getValue();
		
		if ($value == null) {
            return;
        }
	}
	
	protected function _getAttributeOptions($attribute_code)
    {
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
        $options = array();
        foreach( $attribute->getSource()->getAllOptions(true, true) as $option ) {
            if($option['value'] != ""){
				$options[$option['value']] = $option['label'];
			}
        }
        return $options;
    }
}