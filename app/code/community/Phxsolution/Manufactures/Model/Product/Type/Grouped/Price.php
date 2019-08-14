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
class Phxsolution_Manufactures_Model_Product_Type_Grouped_Price extends Mage_Catalog_Model_Product_Type_Grouped_Price
{
	/**
     * Returns product final price depending on options chosen
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = parent::getFinalPrice($qty, $product);
        if ($product->hasCustomOptions()) {
            /* @var $typeInstance Mage_Catalog_Model_Product_Type_Grouped */
            $typeInstance = $product->getTypeInstance(true);
            $associatedProducts = $typeInstance->setStoreFilter($product->getStore(), $product)
                ->getAssociatedProducts($product);
            foreach ($associatedProducts as $childProduct) {
                /* @var $childProduct Mage_Catalog_Model_Product */
                $option = $product->getCustomOption('associated_product_' . $childProduct->getId());
                if (!$option) {
                    continue;
                }
                $childQty = $option->getValue();
                if (!$childQty) {
                    continue;
                }
                $finalPrice += $childProduct->getFinalPrice($childQty) * $childQty;
            }
        }
		
		$profit = $this->addManufactureProfit($product);
		$finalPrice = $finalPrice + $profit;
		
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_type_grouped_price', array('product' => $product));

        return max(0, $product->getData('final_price'));
    }
	
	public function addManufactureProfit($product)
    {
		$productInfo = Mage::getModel('catalog/product')->load($product->getId());
		$profit = 0;
		if($productInfo->getManufacturer()){
			$mInfo =  Mage::getModel('manufactures/manufactures')->getCollection()->addFilter('option_id', $productInfo->getManufacturer())->getData();
			$mInfo = $mInfo[0];
			$price = $product->getPrice();
			if($mInfo["profit_value"] != NULL){
				
				if($mInfo["profit"] == 1){
					$profit = ($price * $mInfo["profit_value"])/100;
				}else{
					$profit = $mInfo["profit_value"];
				}
				return $profit;
			}else{
				return $profit;
			}
		}else{
			return $profit;
		}
	}
}