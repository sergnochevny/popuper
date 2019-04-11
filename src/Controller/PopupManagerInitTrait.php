<?php

namespace Controller;

use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\LeadPopups;
use Popuper\Variables\Repository as EventVariablesRepository;
use Popuper\Variables\RequestData;

/**
 * Trait PopupManagerInitTrait
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
trait PopupManagerInitTrait
{
    /**
     * @var \Popuper\LeadPopups
     */
    protected $_popupsManager;
    /**
     * @var array
     */
    protected $_additionalData;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     */
    public function before()
    {
        parent::before();

        $this->_additionalData = $this->_buildAdditionalData();
        $this->_popupsManager = (new LeadPopups(
            new EventsRepository($this->_leadId),
            new EventVariablesRepository(),
            new PopupBuilderRepository()
        ))
            ->setRequestData(
                $this->_buildRequestData()
            );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     */
    public function after()
    {
        parent::after();

        $this->_popupsManager->setPopupShowed();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return array
     */
    protected function _buildAdditionalData()
    {
        $additionalData = $this->_additionalRequestParams;

        if(isset($additionalData['log'])){
            if(is_array($additionalData['log'])){
                $additionalData['excludeTypes'] = array_unique($additionalData['log']);
            }
            unset($additionalData['log']);
        }

        $additionalData['countryByIP'] = $this->countryByIP;
        $additionalData['calledFrom'] = $this->referrer;
        $additionalData['lang'] = $this->_language;

        $cookies = [];
        foreach ($_COOKIE as $cookieName => $value) {
            $cookies[$cookieName] = $this->_getLeadCookieValue($cookieName, $value);
        }

        $additionalData['cookies'] = $cookies;
        if(!$this->_leadId){
            $additionalData['notAuthedUID'] = $this->_nonAuthId;
        }

        return $additionalData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return \Popuper\Variables\RequestData
     */
    protected function _buildRequestData()
    {
        $requestData = new RequestData();

        if(isset($_COOKIE['lastNotifiedUnsupportedCountry'])){
            $requestData->lastNotifiedUnsupportedCountry =
                $this->_getLeadCookieValue(
                    'lastNotifiedUnsupportedCountry',
                    $_COOKIE['lastNotifiedUnsupportedCountry']
                );
        }
        if(
            isset($this->_additionalRequestParams['log'])
            && is_array($this->_additionalRequestParams['log'])
        ){
            $requestData->excludeTypes = array_unique($this->_additionalRequestParams['log']);
        }

        $requestData->countryByIP = $this->countryByIP;
        $requestData->calledFrom = $this->referrer;
        $requestData->lang = $this->_language;
        $requestData->notAuthedUID = !$this->_leadId
            ? $this->_nonAuthId
            : null;

        if(isset($this->_additionalRequestParams['page']['id'])){
            $requestData->pageId = $this->_additionalRequestParams['page']['id'];
        }

        if(isset($this->_additionalRequestParams['page']['submitFailed'])){
            $requestData->surveySubmitFailed = $this->_additionalRequestParams['page']['submitFailed'];
        }

        $requestData->leadId = $this->_leadId;

        return $requestData;
    }

}