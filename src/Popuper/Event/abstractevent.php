<?php

namespace Popuper\Event;

use Arr;
use Exception;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\Repository;
use Popuper\Variables\Repository as EventVariablesRepository;
use Popuper\Variables\RequestData;

/**
 * Class AbstractEvent
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Event
 */
abstract class AbstractEvent implements InterfaceEvent
{

    /** @var int */
    protected $_id;

    /** @var int */
    protected $_typeId;

    /** @var  int */
    protected $_groupId;

    /** @var int|null */
    protected $_leadId;

    /** @var int */
    protected $_weight = 0;

    /** @var bool */
    protected $_isPermanent = false;

    /** @var bool */
    protected $_enabled = false;

    /** @var bool */
    protected $_allowedToShow = null;

    /** @var RequestData */
    protected $_requestData;

    /** @var Repository */
    protected $_repositoryOfVariables;

    /**
     * @var bool
     */
    protected $_loaded = false;

    /**
     * Function __construct
     * AbstractEvent constructor.
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     *
     * @param int $leadId
     */
    public function __construct($leadId)
    {
        if(!$this->_typeId){
            throw new Exception('Popup Event Type Id is not defined');
        }
        $this->_leadId = $leadId;

        $this->_loadEvent(
            ModelEventType::get($this->_typeId)
        );

        if(!$this->_groupId){
            throw new Exception('Popup Event Group Id is not defined');
        }
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return RequestData
     */
    public function getRequestData()
    {
        if($this->_requestData === null){
            $this->_requestData = new RequestData();
        }

        return $this->_requestData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param RequestData $val
     *
     * @return \Popuper\Event\AbstractEvent
     */
    public function setRequestData(RequestData $val)
    {
        if(!$this->_requestData){
            $this->_requestData = $val;
        }

        return $this;
    }

    /**
     * Function LeadId
     * Set _leadId value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int|null $leadId
     *
     * @return \Popuper\Event\AbstractEvent
     */
    public function setLeadId($leadId)
    {
        $this->_leadId = $leadId;

        return $this;
    }

    /**
     * Function Enabled
     * Get _enabled value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Function isPermanent
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isPermanent()
    {
        return $this->_isPermanent;
    }

    /**
     * get event type id
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @return int
     */
    public function getTypeId()
    {
        return $this->_typeId;
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_loaded;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Function setId
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param $val
     *
     * @return \Popuper\Event\AbstractEvent
     */
    public function setId($val)
    {
        if(!$this->_id){
            $this->_id = $val;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->_groupId;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->_weight;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Variables\Repository $eventVariablesRepository
     * @param \Popuper\Builder\Repository $popupBuilderRepository
     *
     * @return bool
     */
    public function isAllowedToShowPopups(
        EventsRepository $eventsRepository,
        EventVariablesRepository $eventVariablesRepository,
        PopupBuilderRepository $popupBuilderRepository
    ) {
        if($this->_allowedToShow === null){
            $this->_allowedToShow =
                $this->_enabled
                && $this->checkShowRules(
                    $eventsRepository,
                    $eventVariablesRepository,
                    $popupBuilderRepository
                );
        }

        return $this->_allowedToShow;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $param
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     *
     * @return bool|mixed
     */
    public function beforeSaveEvent(
        EventsRepository $param,
        InterfaceEvent $event,
        VariablesInterface $eventData
    ) {
        return true;
    }

    /**
     * Function checkShowRules
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Variables\Repository $eventVariablesRepository
     * @param \Popuper\Builder\Repository $popupBuilderRepository
     *
     * @return bool
     */
    public function checkShowRules(
        EventsRepository $eventsRepository,
        EventVariablesRepository $eventVariablesRepository,
        PopupBuilderRepository $popupBuilderRepository
    ) {
        return true;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $param
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     *
     * @return mixed|void
     */
    public function afterSaveEvent(
        EventsRepository $param,
        InterfaceEvent $event,
        VariablesInterface $eventData
    ) {
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\Model\EventType $eventData
     */
    protected function _loadEvent(ModelEventType $eventData)
    {
        $this->_groupId = (int) Arr::get($eventData, 'groupId');
        $this->_weight = (int) Arr::get($eventData, 'weight');
        $this->_isPermanent = (bool) Arr::get($eventData, 'isPermanent');
        $this->_enabled = (bool) Arr::get($eventData, 'enabled');
        $this->_loaded = true;
    }

}