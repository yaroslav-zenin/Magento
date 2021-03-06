<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Mage_Api_Model_Mysql4_Rules extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('api/rule', 'rule_id');
    }

    public function saveRel(Mage_Api_Model_Rules $rule) {
        $this->_getWriteAdapter()->beginTransaction();

        try {
            $roleId = $rule->getRoleId();
            $this->_getWriteAdapter()->delete($this->getMainTable(), "role_id = {$roleId}");
            $masterResources = Mage::getModel('api/roles')->getResourcesList2D();
            $masterAdmin = false;
            if ( $postedResources = $rule->getResources() ) {
                foreach ($masterResources as $index => $resName) {
                    if ( !$masterAdmin ) {
                        $permission = ( in_array($resName, $postedResources) )? 'allow' : 'deny';
                        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                            'role_type' 	=> 'G',
                            'resource_id' 	=> trim($resName, '/'),
                            'privileges' 	=> '', # FIXME !!!
                            'assert_id' 	=> 0,
                            'role_id' 		=> $roleId,
                            'permission'	=> $permission
                            ));
                    }
                    if ( $resName == 'all' && $permission == 'allow' ) {
                        $masterAdmin = true;
                    }
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (Mage_Core_Exception $e) {
            throw $e;
        } catch (Exception $e){
            $this->_getWriteAdapter()->rollBack();
        }
    }
}
