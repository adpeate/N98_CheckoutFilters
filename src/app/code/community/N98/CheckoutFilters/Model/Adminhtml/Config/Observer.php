<?php
/**
 * netz98 magento module
 *
 * LICENSE
 *
 * This source file is subject of netz98.
 * You may be not allowed to change the sources
 * without authorization of netz98 new media GmbH.
 *
 * @copyright  Copyright (c) 1999-2010 netz98 new media GmbH (http://www.netz98.de)
 * @category N98
 * @package N98_CheckoutFilters
 */
class N98_CheckoutFilters_Model_Adminhtml_Config_Observer
{
    /**
     * Add fields to full system config object
     *
     * @param Varien_Event_Observer $observer
     * @return N98_CheckoutFilters_Model_Adminhtml_Config_Observer
     */
    public function addFieldsToConfig(Varien_Event_Observer $observer)
    {
        /** @var $config Mage_Core_Model_Config_Base */
        $config = $observer->getEvent()->getData('config');

        $sections = $config->getNode('sections');

        foreach ($sections->children() as $section) {
            /** @var $section Mage_Core_Model_Config_Element */
            $this->createConfigFields($section);
        }

        return $this;
    }

    /**
     * Create config field during runtime.
     *
     * @param Varien_Simplexml_Element $section
     * @return N98_CheckoutFilters_Model_Adminhtml_Config_Observer
     */
    public function createConfigFields($section)
    {
        /**
         * Check if we are in sales tab and sub-tab payment or shipping.
         * Then we create SimpleXMLElements for form init.
         */
        if ($section->tab == 'sales') {
            if (in_array($section->getName(), array('carriers', 'payment', 'sagepaysuite'))) {
                foreach ($section->groups[0] as $groupName => $group) {
                    if (isset($group->fields)) {
                        if ($groupName == 'account') {
                            foreach ($group->fields[0] as $name => $fields) {
                                $frontend_model = (string) $fields->frontend_model;
                                if (substr($frontend_model, 0, 6) == 'paypal') {
                                    $groupName = 'allPaypal';
                                    continue;
                                }
                            }
                        } elseif (substr($groupName, 0, 6) == 'paypal') {
                            continue;
                        }
                        $this->_addCustomergroupFieldToConfigGroup($group->fields, $groupName);
                        if (in_array($section->getName(), array('payment', 'sagepaysuite'))) {
                            $this->_addMinYearFieldToConfigGroup($group->fields, $groupName);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $subGroup
     */
    protected function _addMinYearFieldToConfigGroup($fields, $config_path)
    {
        /**
         * Min age in years
         */
        $minAge = $fields->addChild('available_min_age');
        $minAge->addAttribute('translate', 'label');
        /* @var $customerGroup Mage_Core_Model_Config_Element */
        $minAge->addChild('label', 'Min age');
        $minAge->addChild('frontend_type', 'text');
        $minAge->addChild('description', 'age in years');
        $minAge->addChild('sort_order', 1001);
        $minAge->addChild('show_in_default', 1);
        $minAge->addChild('show_in_website', 1);
        $minAge->addChild('show_in_store', 1);
        $minAge->addChild('config_path', 'n98_checkoutfilters/filters/'.$config_path.'/available_min_age');
    }

    /**
     * @param $subGroup
     */
    protected function _addCustomergroupFieldToConfigGroup($fields, $config_path)
    {
        $customerGroup = $fields->addChild('available_for_customer_groups');
        $customerGroup->addAttribute('translate', 'label');
        /* @var $customerGroup Mage_Core_Model_Config_Element */
        $customerGroup->addChild('label', 'Customer Group');
        $customerGroup->addChild('frontend_type', 'multiselect');
        $customerGroup->addChild('source_model', 'adminhtml/system_config_source_customer_group');
        $customerGroup->addChild('sort_order', 1000);
        $customerGroup->addChild('show_in_default', 1);
        $customerGroup->addChild('show_in_website', 1);
        $customerGroup->addChild('show_in_store', 1);
        $customerGroup->addChild('config_path', 'n98_checkoutfilters/filters/'.$config_path.'/available_for_customer_groups');
    }
}
