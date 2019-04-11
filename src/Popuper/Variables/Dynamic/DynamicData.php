<?php

namespace Popuper\Variables\Dynamic;

use Arr;
use Country;
use Mailer;
use Popuper\Variables\RequestData;

/**
 * Class DynamicData
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
class DynamicData extends AbstractVariables
{
    const BILLING_EMAIL = 'BILLING_EMAIL',
        SUPPORT_EMAIL = 'SUPPORT_EMAIL',
        COUNTRY = 'COUNTRY',
        COUNTRY_BY_IP = 'COUNTRY_BY_IP',
        CALLER_PAGE_ID = 'CALLER_PAGE_ID',
        LANGUAGE_CODE = 'lang';

    /** @var RequestData */
    protected $_requestData;
    /** @var LeadData */
    protected $_leadData;

    /**
     * DynamicData constructor.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param RequestData $requestData
     * @param LeadData $leadData
     */
    public function __construct(RequestData $requestData, LeadData $leadData)
    {
        $this->_requestData = $requestData;
        $this->_leadData = $leadData;

        $this->_loadValues();
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return mixed
     */
    public function getCountryName()
    {
        $countriesList = Country::getCountriesByLang($this->_requestData->lang);

        return Arr::get($countriesList, $this->_requestData->countryByIP);
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getSystemVars()
    {
        return [
            self::COUNTRY_BY_IP => 'Lead\'s Country ISO defined by Selected country or IP',
            self::CALLER_PAGE_ID => 'Id of page where popuper is called from',
        ];
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getUsersVars()
    {
        return [
            self::BILLING_EMAIL => 'Billing Department Email address',
            self::SUPPORT_EMAIL => 'Customer Support Email address',
            self::COUNTRY => 'Lead\'s Country defined by Selected country or IP',
        ];
    }

    /**
     * Method loads default values for variables
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     */
    protected function _loadValues()
    {
        $this->_variables[self::BILLING_EMAIL] = Mailer::getEmailAddressByTopic('billing', $this->getCountryName());
        $this->_variables[self::SUPPORT_EMAIL] = Mailer::getEmailAddressByTopic('default', $this->getCountryName());
        $countriesList = Country::getCountriesByLang($this->_requestData->lang);
        $this->_variables[self::COUNTRY] = Arr::get($countriesList, $this->_requestData->countryByIP);
        $this->_variables[self::COUNTRY_BY_IP] = $this->_requestData->countryByIP;

        $this->_variables[self::CALLER_PAGE_ID] = $this->_requestData->pageId;
    }

}
