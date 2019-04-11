<?php

namespace Popuper\Variables\Dynamic;

use F;
use Kohana;
use Arr;
use URL;
use Regulation;
use SystemOptions;

/**
 * Class LinksData
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
class LinksData extends AbstractVariables
{
    const HOME_PAGE_LINK = 'HOME_PAGE_LINK',
        DATA_POLICY_LINK = 'DATA_POLICY_LINK',
        TERMS_LINK = 'TERMS_LINK',
        TERMS_IFRAME_LINK = 'TERMS_IFRAME_LINK',
        REGISTRATION_LINK = 'REGISTRATION_LINK',
        INVESTOR_QUESTIONNAIRE_LINK = 'INVESTOR_QUESTIONNAIRE_LINK',
        KNOWLEDGE_ASSESSMENT_LINK = 'KNOWLEDGE_ASSESSMENT_LINK',
        PLATFORM_LINK = 'PLATFORM_LINK',
        ACCOUNT_VERIFICATION_PAGE = 'ACCOUNT_VERIFICATION_PAGE',
        DEPOSIT_PAGE_LINK = 'DEPOSIT_PAGE_LINK',
        POPUPER_HOST = 'POPUPER_HOST',
        ASSETS_POPUPER_HOST = 'ASSETS_POPUPER_HOST';

    /** @var string */
    protected $_host;

    /** @var string */
    protected $_lang;

    /** @var int */
    protected $_countryType;

    /**
     * LinksData constructor.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param string $lang
     */
    public function __construct($lang)
    {
        $this->_lang = $lang;

        $this->_loadValues();
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     * @return mixed|string
     */
    public function getHost()
    {
        if(!$this->_host){
            $defaultLanguage = Kohana::config('global/languages')
                ->get('default');

            /** @var array $sites */
            $sites = (Kohana::config('global/sites-types.sites'));
            $this->_host = Arr::get(
                $sites,
                $this->_lang,
                Arr::get(
                    $sites,
                    $defaultLanguage,
                    ''
                )
            );

            $this->_host = '//' . URL::parse($this->_host, PHP_URL_HOST);

            $this->_host = Regulation::instance()
                ->parseUrlBasedOnCurrentDomain($this->_host);
        }

        return $this->_host;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return mixed
     */
    public function getSiteLink()
    {
        return Regulation::instance()
            ->updateDomainForCountryType(
                $this->getHost(),
                $this->_getCountryType()
            );
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
            self::POPUPER_HOST => 'Popuper',
            self::ASSETS_POPUPER_HOST => 'Popuper Assets Host',
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
            self::HOME_PAGE_LINK => 'Link to the Main Home page',
            self::DATA_POLICY_LINK => 'Link to the Data Policy page',
            self::TERMS_LINK => 'Link to the Terms and Conditions page',
            self::TERMS_IFRAME_LINK => 'Link to the Terms and Conditions page under iFrame view',
            self::REGISTRATION_LINK => 'Link to the Registration page',
            self::INVESTOR_QUESTIONNAIRE_LINK => 'Link to the Investor Questionnaire page',
            self::KNOWLEDGE_ASSESSMENT_LINK => 'Link to the Knowledge Assessment page',
            self::PLATFORM_LINK => 'Link to the Platform page',
            self::ACCOUNT_VERIFICATION_PAGE => 'Link to the Account Verification page',
            self::DEPOSIT_PAGE_LINK => 'Link to the Platform Depositing page',
        ];
    }

    /**
     * Function _getCountryType
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return mixed
     */
    protected function _getCountryType()
    {
        if(!$this->_countryType){
            $countryTypesIdentifier = (new \CountryTypesConfigurator\Identifier());
            $this->_countryType = $countryTypesIdentifier->getDefaultCountryType();
        }

        return $this->_countryType;
    }

    /**
     * Function CountryType
     * Set _countryType value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int $countryType
     */
    public function setCountryType($countryType)
    {
        $this->_countryType = $countryType;
    }

    /**
     * Method loads default values for variables
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Kohana_Exception
     */
    protected function _loadValues()
    {
        $this->_variables[self::HOME_PAGE_LINK] = $this->getHost() . '/';

        $policyUri = trim(SystemOptions::get('System/Website/PrivacyPolicy page URI'), '/');
        $this->_variables[self::DATA_POLICY_LINK] = "{$this->getHost()}/{$policyUri}/";

        $termsUri = trim(SystemOptions::get('System/Website/T&C page URI'), '/');
        $this->_variables[self::TERMS_LINK] = "{$this->getHost()}/{$termsUri}/";

        $iframeTermsUri = trim(SystemOptions::get('System/Website/T&C iframe page URI'), '/');
        $this->_variables[self::TERMS_IFRAME_LINK] = "{$this->getHost()}/{$iframeTermsUri}/";

        $registrationUri = trim(SystemOptions::get('System/Website/Registration page URI'), '/');
        $this->_variables[self::REGISTRATION_LINK] = "{$this->getHost()}/{$registrationUri}/";

        $surveyUri = trim(SystemOptions::get('System/Website/Survey page URI'), '/');
        $this->_variables[self::INVESTOR_QUESTIONNAIRE_LINK] = "{$this->getHost()}/{$surveyUri}/";

        $quizUri = trim(SystemOptions::get('System/Website/Quiz page URI'), '/');
        $this->_variables[self::KNOWLEDGE_ASSESSMENT_LINK] = "{$this->getHost()}/{$quizUri}/";

        $hostPlatform = '//' . trim(SystemOptions::get('System/Outer Services/Platform Domain'), '/');
        $hostPlatform = Regulation::instance()
            ->parseUrlBasedOnCurrentDomain(
                '//' . URL::parse($hostPlatform, PHP_URL_HOST)
            );
        $this->_variables[self::PLATFORM_LINK] = "{$hostPlatform}/";

        $platformVerification = trim(SystemOptions::get('System/Platform/Verification page URI'), '/');
        $this->_variables[self::ACCOUNT_VERIFICATION_PAGE] =
            "{$this->_variables[self::PLATFORM_LINK]}{$platformVerification}/";

        $platformDeposit = trim(SystemOptions::get('System/Platform/Deposit page URI'), '/');
        $this->_variables[self::DEPOSIT_PAGE_LINK] = "{$this->_variables[self::PLATFORM_LINK]}{$platformDeposit}/";

        $this->_variables[self::ASSETS_POPUPER_HOST] = rtrim(
                                                           \PopuperAssets::instance()
                                                               ->getHost(), '/'
                                                       ) . '/';

        $popuperDomain = trim(
            F::getCookieDomain(
                SystemOptions::get('System//Cookie Domain Level')
            ),
            '/'
        );
        if(strpos($popuperDomain, '.popuper.') !== 0 && strpos($popuperDomain, 'popuper.') !== 0){
            $popuperDomain = "popuper{$popuperDomain}";
        }
        $popuperDomain = ltrim($popuperDomain, '.');
        $this->_variables[self::POPUPER_HOST] = "//$popuperDomain/";
    }
}
