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
 * @category   Manufactures Database Table Setup
 * @package    Phxsolution_Manufactures
 * @author     Prakash Vaniya 
 * @contact    contact@phxsolution.com 
 * @site       www.phxsolution.com 
 * @copyright  Copyright (c) 2014 PHXSolution Manufactures
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('manufactures')};
CREATE TABLE {$this->getTable('manufactures')} (
  `manufactures_id` int(11) unsigned NOT NULL auto_increment,
  `attribute_id` smallint(6) NOT NULL default '0',
  `option_id` smallint(6) NOT NULL default '0',
  `profit_value` int(11) NOT NULL default '0',
  `profit` smallint(6) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `note` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`manufactures_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();