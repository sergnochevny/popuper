<?php

namespace Popuper\Variables;

use Model_CountryType;

/**
 * Class RequestData
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables
 */
class RequestData
{

    const CALLED_FROM = 'calledFrom';
    const LANGUAGE_CODE = 'lang';

    /** @var mixed */
    public $lastNotifiedUnsupportedCountry;
    /** @var array */
    public $excludedTypes = [];
    /** @var string */
    public $countryByIP;
    /** @var string */
    public $calledFrom;
    /** @var string */
    public $lang;
    /** @var mixed */
    public $notAuthedUID;
    /** @var integer */
    public $leadId;
    /** @var boolean */
    public $forMobile = false;
    /** @var string */
    public $pageId;
    /** @var mixed */
    public $surveySubmitFailed;
    /** @var integer|null */
    protected $_countryType;

    /**
     * Returns country type based on url and ip.
     * Should be used for non logged in lead.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return int|null
     */
    public function getCountryType()
    {
        if(!$this->_countryType){
            $this->_countryType = Model_CountryType::getCountryTypeForLead(
                $this->calledFrom,
                $this->countryByIP,
                false
            );
        }

        return $this->_countryType;
    }

    /**
     * Returns allowed variables for popup.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getAllowedVarListWithDescription()
    {
        return [];
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            self::CALLED_FROM => $this->calledFrom,
            self::LANGUAGE_CODE => $this->lang,
        ];
    }
}
