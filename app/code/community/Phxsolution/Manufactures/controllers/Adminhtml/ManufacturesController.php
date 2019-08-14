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
 * @category   Manufactures Controllers
 * @package    Phxsolution_Manufactures
 * @author     Prakash Vaniya 
 * @contact    contact@phxsolution.com 
 * @site       www.phxsolution.com 
 * @copyright  Copyright (c) 2014 PHXSolution Manufactures
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php
class Phxsolution_Manufactures_Adminhtml_ManufacturesController extends Mage_Adminhtml_Controller_action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('manufactures/items')
			->_title($this->__('Manufactures List'))
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manufacters List'), Mage::helper('adminhtml')->__('Manufacters List'));
		
		return $this;
	}   
 	public function indexAction()
 	{
		$manufacturesCollection = Mage::getModel('manufactures/manufactures')->getCollection();
        if(!count($manufacturesCollection))
        	Mage::helper('manufactures')->installSampleData();

		$this->_initAction()
			->renderLayout();
	}
	public function editAction()
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('manufactures/manufactures')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('manufactures_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('manufactures/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manufacters List'), Mage::helper('adminhtml')->__('Manufacters List'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('manufactures/adminhtml_manufactures_edit'))
				->_addLeft($this->getLayout()->createBlock('manufactures/adminhtml_manufactures_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufactures')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction()
	{
		$this->_forward('edit');
	}
 	public function saveAction()
 	{
		if ($data = $this->getRequest()->getPost())
		{
			$option_id = $this->getRequest()->getParam('option_id');
			$id = $this->getRequest()->getParam('id');
			$model = Mage::getModel('manufactures/manufactures');		
			$model->setData($data)
				->setId($id);
						
			try
			{
				// save image
				if((bool)$data['image']['delete']==1)
				{
					$data['image']='';
				}
				else
				{
					if (isset($_FILES))
					{
						if ($_FILES['image']['name'])
						{
							unset($data['image']);
							if($id)
							{
								if($model->getData('image'))
								{
									$io = new Varien_Io_File();
									$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('image'))));	
								}
							}
							$path = Mage::getBaseDir('media') . DS . 'manufactures' . DS .'logos'.DS;
							$uploader = new Varien_File_Uploader('image');
							$uploader->setAllowedExtensions(array('jpg','png','gif'));
							$uploader->setAllowRenameFiles(false);
							$uploader->setFilesDispersion(false);
							$destFile = $path.$_FILES['image']['name'];
							$filename = $uploader->getNewFileName($destFile);
							$uploader->save($path, $filename);							
							
							$model['image'] = 'manufactures/logos/'.$filename;
						}
						else
						{
							if($data['image']['value'])
								$model['image'] = $data['image']['value'];
						}
							
					}
					else
						Mage::getSingleton('adminhtml/session')->addError('_FILES not set');
				}
				// save image

				if($note = $this->getRequest()->getParam('note'))
					$model->setNote($note);
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
				{
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				}
				else
				{
					$model->setUpdateTime(now());
				}	
				
				$available = $this->checkExist($option_id, $id);
				if($available == NULL)
				{
					$model->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('manufactures')->__('Item was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
				}
				else
				{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufactures')->__('The Manufacture has already assigned profile value'));
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				if ($this->getRequest()->getParam('back'))
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manufactures')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 	public function checkExist($option_id, $id)
 	{
		$modelInfo = Mage::getModel('manufactures/manufactures')->getCollection()->addFilter('option_id', $option_id)->getData();
		if($modelInfo[0]["manufactures_id"] == $id){
			return;
		}else{
			return $modelInfo[0]["manufactures_id"];
		}
		return;
	}
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('manufactures/manufactures');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function massDeleteAction()
	{
        $manufacturesIds = $this->getRequest()->getParam('manufactures');
        if(!is_array($manufacturesIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($manufacturesIds as $manufacturesId) {
                    $manufactures = Mage::getModel('manufactures/manufactures')->load($manufacturesId);
                    $manufactures->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($manufacturesIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function massProfitAction()
    {
        $manufacturesIds = $this->getRequest()->getParam('manufactures');
        if(!is_array($manufacturesIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($manufacturesIds as $manufacturesId) {
                    $manufactures = Mage::getSingleton('manufactures/manufactures')
                        ->load($manufacturesId)
                        ->setProfit($this->getRequest()->getParam('profit'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($manufacturesIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  	public function exportCsvAction()
    {
        $fileName   = 'manufactures.csv';
        $content    = $this->getLayout()->createBlock('manufactures/adminhtml_manufactures_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
    public function exportXmlAction()
    {
        $fileName   = 'manufactures.xml';
        $content    = $this->getLayout()->createBlock('manufactures/adminhtml_manufactures_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}