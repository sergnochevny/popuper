<?php

namespace Popuper\Variables;

use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\CustomData;
use Popuper\Variables\Dynamic\DynamicData;
use Popuper\Variables\Dynamic\EventData;
use Popuper\Variables\Dynamic\LeadData;
use Popuper\Variables\Dynamic\LinksData;

/**
 * Class Repository
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables
 */
class Repository
{
    /** @var integer */
    protected $_eventId;

    /** @var integer */
    protected $_eventTypeId;

    /** @var RequestData */
    protected $_requestData;

    /** @var LeadData */
    protected $_leadData;

    /** @var EventData */
    protected $_eventData;

    /** @var CustomData */
    protected $_customData;

    /** @var DynamicData */
    protected $_dynamicData;

    /** @var LinksData */
    protected $_linksData;

    /** @var array */
    protected $_dataForReplace = [];

    /** @var array */
    protected $_allowedListWithDescriptions = [];

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return int
     */
    public function getEventId()
    {
        return $this->_eventId;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param integer $val
     *
     * @return \Popuper\Variables\Repository
     */
    public function setEventId($val)
    {
        $this->_eventId = $val;

        return $this;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return int
     */
    public function getEventTypeId()
    {
        return $this->_eventTypeId;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param integer $val
     *
     * @return \Popuper\Variables\Repository
     */
    public function setEventTypeId($val)
    {
        $this->_eventTypeId = $val;

        return $this;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return RequestData
     */
    public function getRequestData()
    {
        return $this->_requestData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param RequestData $val
     *
     * @return \Popuper\Variables\Repository
     */
    public function setRequestData(RequestData $val)
    {
        $this->_requestData = $val;

        return $this;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     * @return LeadData
     */
    public function getLeadData()
    {
        if(!$this->_leadData && $this->getRequestData()){
            $this->_leadData = new LeadData(
                $this->getRequestData()
            );
        }

        return $this->_leadData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return EventData
     */
    public function getEventData()
    {
        if(!$this->_eventData){
            $this->_eventData = new EventData(
                $this->getEventId(),
                $this->getEventTypeId()
            );
        }

        return $this->_eventData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return CustomData
     */
    public function getCustomData()
    {
        if(!$this->_customData && $this->getRequestData()){
            $this->_customData = new CustomData($this->getRequestData()->lang);
        }

        return $this->_customData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     * @return DynamicData
     */
    public function getDynamicData()
    {
        if(!$this->_dynamicData && $this->getLeadData()){
            $this->_dynamicData = new DynamicData(
                $this->getRequestData(),
                $this->getLeadData()
            );
        }

        return $this->_dynamicData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Kohana_Exception
     * @return LinksData
     */
    public function getLinksData()
    {
        if(
            !$this->_linksData && $this->getRequestData()
        ){
            $this->_linksData = new LinksData(
                $this->getRequestData()->lang
            );
        }

        return $this->_linksData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     * @return array
     */
    public function getDataForReplace()
    {
        if(!$this->_dataForReplace){
            if($customData = $this->getCustomData()){
                $this->_dataForReplace = $customData->getValues();
            }

            if($linksData = $this->getLinksData()){
                $this->_dataForReplace = array_merge(
                    $this->_dataForReplace,
                    $linksData->getValues()
                );
            }
            if($leadData = $this->getLeadData()){
                $this->_dataForReplace = array_merge(
                    $this->_dataForReplace,
                    $leadData->getValues()
                );
            }

            $this->_dataForReplace = array_merge(
                $this->_dataForReplace,
                $this->getRequestData()
                    ->asArray()
            );

            if($dynamicData = $this->getDynamicData()){
                $this->_dataForReplace = array_merge(
                    $this->_dataForReplace,
                    $dynamicData->getValues()
                );
            }

            if($eventData = $this->getEventData()){
                $this->_dataForReplace = array_merge(
                    $this->_dataForReplace,
                    $eventData->getValues()
                );
            }
        }

        return $this->_dataForReplace;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     *
     * @param null|int $visibilityType - see available values in Popuper\Model\EventVariable class
     *
     * @return array _allowedListWithDescriptions with order of keys as it will be shown in the select
     */
    public function getAllowedListWithDescriptions($visibilityType = null)
    {
        if(!$this->_allowedListWithDescriptions){
            $labelDynamic = __('Dynamic Variables');
            $labelEvent = __('Event Variables');
            $labelLead = __('Lead Variables');
            $labelLinks = __('Links Variables');
            $labelCustom = __('Custom Variables');

            $dynamicData = $this->getDynamicData();
            $eventData = $this->getEventData();
            $leadData = $this->getLeadData();
            $linksData = $this->getLinksData();
            $customData = $this->getCustomData();

            if($visibilityType === EventVariable::TYPE_IS_USER){
                $this->_allowedListWithDescriptions = [
                    $labelDynamic => $dynamicData->getUsersVars(),
                    $labelEvent => $eventData->getUsersVars(),
                    $labelLead => $leadData->getUsersVars(),
                    $labelLinks => $linksData->getUsersVars(),
                    $labelCustom => $customData->getUsersVars(),
                ];
            } else {
                $this->_allowedListWithDescriptions = [
                    $labelDynamic => $dynamicData->getAllowedVarListWithDescription(),
                    $labelEvent => $eventData->getAllowedVarListWithDescription(),
                    $labelLead => $leadData->getAllowedVarListWithDescription(),
                    $labelLinks => $linksData->getAllowedVarListWithDescription(),
                    $labelCustom => $customData->getAllowedVarListWithDescription(),
                ];
            }
        }

        return $this->_allowedListWithDescriptions;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @throws \Exception
     * @return array
     */
    public function getAllowedList()
    {
        $result = [];
        foreach ($this->getAllowedListWithDescriptions() as $allowedListWithDescription) {
            array_merge($result, $allowedListWithDescription);
        }

        return array_values($this->getAllowedListWithDescriptions());
    }
}
