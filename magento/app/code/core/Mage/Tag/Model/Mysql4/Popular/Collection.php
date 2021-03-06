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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Popular tags collection model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Popular_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

    /**
     * Replacing popularity by sum of popularity and base_popularity
     *
     * @param int $storeId
     * @return Mage_Tag_Model_Mysql4_Popular_Collection
     */
    public function joinFields($storeId = 0)
    {
        $this->getSelect()
            ->reset()
            ->from(
                array('tag_summary' => $this->getTable('tag/summary')),
                array(
                    'tag_id',
                    'popularity'
                )
            )
            ->joinInner(
                array('tag' => $this->getTable('tag/tag')),
                'tag.tag_id = tag_summary.tag_id AND tag.status = '.Mage_Tag_Model_Tag::STATUS_APPROVED
            )
            ->where('tag_summary.store_id = ?', $storeId)
            ->where('tag_summary.products > 0')
            ->order('popularity desc');
        return $this;
    }

    /**
     * Add filter by specified tag status
     *
     * @param string $statusCode
     * @return Mage_Tag_Model_Mysql4_Popular_Collection
     */
    public function addStatusFilter($statusCode)
    {
        $this->getSelect()->where('main_table.status = ?', $statusCode);
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }

    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }
}
