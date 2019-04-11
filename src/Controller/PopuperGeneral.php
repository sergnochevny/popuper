<?php

namespace Controller;

use Arr;
use AuthorizationException;
use Controller_Template;
use Cookie;
use F;
use I18n;
use Kohana;
use Kohana_Exception404;
use LeadAuthorizationByCookie;
use Popuper\LeadPopups;
use PopuperAssets;
use Regulation;
use Request;
use URL;

/**
 * Class PopuperGeneral
 * General controller for pop-up.
 * It ties to get Lead ID from cookies and set it to needed property.
 * It tries to get unique notAuthedUID value and set it to needed property If no lead ID founded.
 * It tries to get current site language and set it to needed property.
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
abstract class PopuperGeneral extends Controller_Template
{

    /** @var string */
    public $ip = '';

    /** @var string */
    public $countryByIP = '';

    /** @var string */
    public $contentType = '';

    /** @var string */
    public $referrer = '';

    /** @var int|null _leadId */
    protected $_leadId = null;

    /** @var int|null _nonAuthId */
    protected $_nonAuthId = null;

    /** @var string|null _language Current site's language. As default en */
    protected $_language;

    /** @var array */
    protected $_additionalRequestParams = [];

    /** @var array */
    protected $_requestAdditionalKeys = [
        'page', 'log',
    ];

    /**
     * Function before
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     */
    public function before()
    {
        if(!LeadPopups::isEnabled()){
            throw new Kohana_Exception404();
        }

        if(!$this->referrer){
            $this->referrer = (Request::$referrer)
                ?: (
                    URL::base(false, true) .
                    trim(Arr::get($_SERVER, 'REQUEST_URI'), '/')
                );
        }

        if($this->contentType){
            $this->request->headers['Content-Type'] = $this->contentType;
        }

        parent::before();

        /**
         * @var string ip Get IP from our custom method
         * because Kohana_Request can get's not really actual address but proxy
         */
        $this->ip = F::getRealIpAddr();
        $this->countryByIP = F::getCountryByIP($this->ip, 'country_code');
        $this->countryByIP = (Kohana::$environment < Kohana::TESTING)
            ?:
            'UA';

        $this->_leadId = $this->_getNonLeadIdByCookies();
        $this->_nonAuthId = $this->_getNonAuthIdByCookies();
        $this->_language = $this->_getLanguageByUrl($this->referrer);

        I18n::lang($this->_language);

        $this->_additionalRequestParams = $this->_getAdditionalRequestParams();
    }

    /**
     * Function _getNonLeadIdByCoockies
     * Find lead ID from cookies
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return int|null
     */
    protected function _getNonLeadIdByCookies()
    {
        $leadId = null;

        $hashId = $this->_getLeadCookieValue(
            Kohana::config('leadAuthorizationCookiesHash.hashIdCookieName')
        );
        $hashSumm = $this->_getLeadCookieValue(
            Kohana::config('leadAuthorizationCookiesHash.hashSumCookieName')
        );

        if(!$hashId || !$hashSumm){
            return $leadId;
        }

        try {
            /*
             * @var object $lead LeadAuthorizationByCookie
             */
            $lead = new LeadAuthorizationByCookie();

            /** Do not check last auth country and do not log this auth */
            $lead->disableAuthLog();

            /*
             * @var object $lead LeadAuthorizationByCookie
             * Try to do authorization
             * @throws AuthorizationException
             */
            $lead->authentication(
                [
                    'hashId' => $hashId,
                    'hashedLeadData' => $hashSumm,
                    'ip' => $this->ip,
                    'url' => $this->referrer,
                ]
            );

            $leadId = $lead->getParamLeadId();
        } catch (AuthorizationException $exception) {
            $leadId = null;
        }

        return $leadId;
    }

    /**
     * Function _getNonAuthIdByCoockies
     * Find Non Auth ID from cookies
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return null
     */
    protected function _getNonAuthIdByCookies()
    {
        return
            (
                !$this->_leadId
                && (
                $nonAuthId = $this->_getLeadCookieValue(
                    Kohana::config(
                        'leadAuthorizationCookiesHash.nonAuthIdCookiesName'
                    )
                ))
            )
                ? $nonAuthId
                : null;
    }

    /**
     * Function _getLanguageByUrl
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param $url
     *
     * @return bool|string
     */
    protected function _getLanguageByUrl($url)
    {
        if($url){
            $urlParts = parse_url($url);
            $url = Arr::get($urlParts, 'host') . Arr::get($urlParts, 'path');
            $url = rtrim($url, '/') . '/';
        }

        /** @var array $sites */
        $sites = (Kohana::config('global/sites-types.sites'))
            ?: [];

        /** @var string $defaultLang */
        $defaultLang = Kohana::config('global/languages')
            ->get('default', '');

        /** DO not check on default lang because we will return it if no any lang be founded */
        unset($sites[$defaultLang]);

        foreach ($sites as $language => $siteAddress) {
            $siteAddress = rtrim($siteAddress, '/') . '/';

            if(strpos($url, $siteAddress) === 0 || strpos($url, '.' . $siteAddress)){
                return substr($language, 0, 2);
            }
        }

        /** @var string $languageByCookies */
        $languageByCookies = $this->_getLeadCookieValue(
            Kohana::config('leadAuthorizationCookiesHash.lastLanguageCookiesName')
        );

        /** if we have some language value from cookies - and it is valid - use it as default */
        if($languageByCookies && isset($sites[$languageByCookies])){
            return $languageByCookies;
        }

        return $defaultLang;
    }

    /**
     * Function _getLeadCookieValue
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param null $default
     * @param $cookieName
     *
     * @return mixed|null|string
     */
    protected function _getLeadCookieValue($cookieName, $default = null)
    {
        return ($cookieName)
            ? Cookie::get($cookieName, $default, Kohana::config('leadAuthorizationCookiesHash.salt'))
            : $default;
    }

    /**
     * Function _getAdditionalRequestParams
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    protected function _getAdditionalRequestParams()
    {
        return array_filter(
            Arr::extract($_GET, $this->_requestAdditionalKeys)
        );
    }

    /**
     * Function _checkListOfAdditionalFiles
     * Check each row in array. if no host in it - add current assets host
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $list
     *
     * @return mixed
     */
    protected function _checkListOfAdditionalFiles($list)
    {
        foreach ($list as $index => $file) {
            if(!URL::parse($file, PHP_URL_HOST)){
                $list[$index] = Regulation::instance()
                    ->parseUrlBasedOnCurrentDomain(
                        PopuperAssets::instance()
                            ->url($file)
                    );
            }
        }

        return $list;
    }

}
