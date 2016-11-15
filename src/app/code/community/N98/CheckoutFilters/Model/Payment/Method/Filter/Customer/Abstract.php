<?php

abstract class N98_CheckoutFilters_Model_Payment_Method_Filter_Customer_Abstract
    extends N98_CheckoutFilters_Model_Payment_Method_Filter_Abstract
{
    const XML_FILTER_PREFIX = 'n98_checkoutfilters/filters/';

    abstract protected function _getConfigSuffix();

    protected function _getConfigData($code) {
        if (substr($code, 0, 6) == 'paypal') {
            $code = 'allPaypal';
        }
        return Mage::getStoreConfig(self::XML_FILTER_PREFIX . $code . $this->_getConfigSuffix());
    }
}

