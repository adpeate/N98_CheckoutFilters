<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cmuench
 * Date: 15.06.12
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
class N98_CheckoutFilters_Model_Payment_Method_Filter_Customer_Group
    extends N98_CheckoutFilters_Model_Payment_Method_Filter_Customer_Abstract
    implements N98_CheckoutFilters_Model_Payment_Method_Filter
{
    /**
     * @var string
     */
    const XML_FILTER_SUFFIX = '/available_for_customer_groups';

    /**
     * @return void
     */
    public function filter()
    {
        $customer = $this->_getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */

        $paymentMethodInstance = $this->getMethodInstance();

        $customerGroupConfig = $this->_getConfigData($paymentMethodInstance->getCode());

        if (!empty($customerGroupConfig)) {
            $methodCustomerGroups = explode(',', $customerGroupConfig);
            if (count($methodCustomerGroups) > 0) {
                if (!in_array($customer->getGroupId(), $methodCustomerGroups)) {
                    $this->getResult()->isAvailable = false;
                }
            }
        }
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::helper('customer')->getCustomer();
    }

    protected function _getConfigSuffix()
    {
        return self::XML_FILTER_SUFFIX;
    }

}
