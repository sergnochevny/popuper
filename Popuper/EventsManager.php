<?php

namespace Popuper;

use Exception;
use Popuper\Event\AbstractEvent;
use Popuper\Variables\Repository as EventVariablesRepository;
use Popuper\Variables\RequestData;

/**
 * Class EventsManager
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper
 */
class EventsManager
{
    /**
     * @var \Popuper\EventsRepository
     */
    protected $_eventsRepository;

    /**
     * @var \Popuper\Variables\Repository
     */
    protected $_eventVariablesRepository;

    /**
     * @var AbstractEvent|null
     */
    protected $_event;

    /**
     * @var RequestData
     */
    protected $_requestData;

    /**
     * @var mixed
     */
    protected $_eventData;

    /**
     * Function __construct
     * EventsManager constructor.
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Variables\Repository $eventVariablesRepository
     */
    public function __construct(
        EventsRepository $eventsRepository,
        EventVariablesRepository $eventVariablesRepository
    ) {
        $this->_eventsRepository = $eventsRepository;

        $this->_eventVariablesRepository = $eventVariablesRepository
            ->setRequestData(
                $eventsRepository->getRequestData()
            );
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return RequestData
     */
    public function getRequestData()
    {
        if(!$this->_requestData){
            $this->_requestData = $this->_eventsRepository->getRequestData();
        }

        return $this->_requestData;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param RequestData $val
     *
     * @return \Popuper\EventsManager
     */
    public function setRequestData(RequestData $val)
    {
        if(!$this->_requestData){
            $this->_requestData = $val;
        }

        return $this;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     * @throws \Exception
     *
     * @param array $dataVariables
     *
     * @return bool
     */
    public function saveCurrentEvent(array $dataVariables = [])
    {
        $event = $this->getEvent();
        $eventData = $this->getEventData()
            ->setValues($dataVariables);

        return $this->_eventsRepository
            ->saveEvent($event, $eventData);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Exception
     * @throws \Exception
     * @return bool
     */
    public function removeCurrentEvent()
    {
        $event = $this->getEvent();
        if($result = $this->_eventsRepository->removeEvent($event)){
            $this->resetEvent();
        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Exception
     * @throws \Exception
     * @return $this
     */
    public function setCurrentEventAsHandled()
    {
        if($event = $this->getEvent()){
            $this->_eventsRepository->setEventAsHandled(
                $event,
                $this->getEventData()
            );
        }

        return $this;
    }

    /**
     * Function Event
     * Get _event value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return AbstractEvent
     */
    public function getEvent()
    {
        if(!$this->_event){
            $this->_event = $this->_findNextEvent();
        }

        return $this->_event;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     * @return mixed|\Popuper\Variables\Dynamic\EventData
     */
    public function getEventData()
    {
        if(!$this->_eventData){
            $event = $this->getEvent();

            $this->_eventData
                = $this->_eventVariablesRepository
                ->setEventId($event->getId())
                ->setEventTypeId($event->getTypeId())
                ->getEventData();
        }

        return $this->_eventData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $typeId
     *
     * @return $this
     */
    public function setEventTypeId($typeId)
    {
        $this->_eventsRepository->setTypeId($typeId);

        return $this;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     *
     * @param $Id
     *
     * @return $this
     */
    public function setEventId($Id)
    {
        $this->getEvent()
            ->setId($Id);

        return $this;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return $this
     */
    public function resetEvent()
    {
        $this->_event = null;
        $this->_eventData = null;

        return $this;
    }

    /**
     * Function _findNextEvent
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws Exception
     * @return \Popuper\Event\AbstractEvent
     */
    protected function _findNextEvent()
    {
        return EventsFactory::getEvent(
            $this->_eventsRepository->getTypeId(),
            $this->_eventsRepository->getLeadId()
        )
            ->setRequestData(
                $this->getRequestData()
            );
    }

}