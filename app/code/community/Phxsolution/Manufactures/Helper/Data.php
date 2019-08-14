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
 * @category   Manufactures Helper
 * @package    Phxsolution_Manufactures
 * @author     Prakash Vaniya 
 * @contact    contact@phxsolution.com 
 * @site       www.phxsolution.com 
 * @copyright  Copyright (c) 2014 PHXSolution Manufactures
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Manufactures_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getIsEnabled()
    {
      return Mage::getStoreConfig('manufactures_section/settings/enabled');
    }
    public function getNoImage()
    {
      $noImage = 'manufactures/logos/'.Mage::getStoreConfig('manufactures_section/settings/logo');
      if(Mage::getStoreConfig('manufactures_section/settings/logo'))
        return $noImage;
      return '';
    }
    public function getManufacturesLabel($manufactures_id)
    {
      $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
        $allOptions = $attribute->getSource()->getAllOptions(true, true);
        foreach ($allOptions as $key => $value)
        {
            if($manufactures_id==$value['value'])
                return $value['label'];
        }
    }
    public function checkImage($image)
    {
      //checks if the file is a browser compatible image
      $mimes = array('image/gif','image/jpeg','image/pjpeg','image/png');
      //get mime type
      $mime = getimagesize($image);
      $mime = $mime['mime'];

      $extensions = array('jpg','png','gif','jpeg');
      $extension = strtolower( pathinfo( $image, PATHINFO_EXTENSION ) );

      if ( in_array( $extension , $extensions ) AND in_array( $mime, $mimes ) )
        return true; 
      else
        return false; 
    }
    public function installSampleData()
    {
        // installs default data to manufactures list
        $manufactures = array(
            array(
                'attribute_id' => 102,
                'option_id' => 1,
                'profit_value' => 10,
                'profit' => 1,
                'image' => '',
                'note' => 'Test note.',
                'created_time' => now(),
                'update_time' => now(),
            ),
            array(
                'attribute_id' => 102,
                'option_id' => 2,
                'profit_value' => 10,
                'profit' => 2,
                'image' => '',
                'note' => 'Test note.',
                'created_time' => now(),
                'update_time' => now(),
            ),
        );
        foreach ($manufactures as $item) {
            Mage::getModel('manufactures/manufactures')
                ->setData($item)
                ->save();
        }
    }
    public function formatPrice($price)
    {
        $returnPrice = $price;

        list($first,$decimal) = explode('.', $price);
        if($decimal>0)
            $returnPrice = number_format($price,2);
        else
            $returnPrice = $first;
        return Mage::helper('core')->currency($returnPrice,true,false);
    }
    public function getPriceHtml($_product)
    {
        $price = $_product->getPrice();
        $finalPrice = $_product->getFinalPrice();
        $html = '';

        if($price>0 || $finalPrice>0)
        {
            $formattedPrice = $this->formatPrice($price);
            $formattedFinalPrice = $this->formatPrice($finalPrice);

            $html .= '<div class="price-box">';
            if($finalPrice<$price)
            {
                $html .= '<p class="old-price">';
                    $html .= '<span class="price-label">Regular Price:</span>';
                    $html .= '<span class="price">';
                        $html .= $formattedPrice;
                    $html .= '</span>';
                $html .= '</p>';

                $html .= '<p class="special-price">';
                    $html .= '<span class="price-label">Special Price:</span>';
                    $html .= '<span class="price">';
                        $html .= $formattedFinalPrice;
                    $html .= '</span>';
                $html .= '</p>';
            }
            elseif($finalPrice>$price)
            {
                $html .= '<span class="regular-price">';
                    $html .= '<span class="price">'.$formattedFinalPrice.'</span>';
                $html .= '</span>';
            }
            else
            {
                $html .= '<span class="regular-price">';
                    $html .= '<span class="price">'.$formattedPrice.'</span>';
                $html .= '</span>';
            }
            $html .= '</div>';
        }
        return $html;
    }
}