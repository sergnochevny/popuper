<?php

namespace Popuper;

use Arr;
use Popuper\Event\InterfaceEvent;
use Popuper\Model\Event as ModelEvent;
use Popuper\Model\EventsLog as ModelEventsLog;
use Popuper\Model\EventsLogStatuses as ModelEventsLogStatuses;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\RequestData;

/**
 * Class EventsRepository
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class EventsRepository
{
    /**
     * @var int|null
     */
    protected $_leadId;
    /**
     * @var int|null
     */
    protected $_typeId;

    /**
     * Function __construct
     * EventsManager constructor.
     *
     * @param int|null $leadId
     */
    public function __construct($leadId)
    {
        $this->_leadId = $leadId;
    }

    /**
     * Function removeEvent
     * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param string $reason
     * @param \Popuper\Event\InterfaceEvent $event
     *
     * @return bool
     */
    public function removeEvent(InterfaceEvent $event, $reason = 'Remove event')
    {
        if($event->isEnabled()){
            /** @var array $existEvents */
            $existEvents = ModelEvent::get($this->_leadId, $this->_typeId, true);

            if($existEvents){
                foreach ($existEvents as $eventId => $eventData) {
                    ModelEventsLog::save(
                        $eventData,
                        ModelEventsLogStatuses::REMOVED,
                        $reason
                    );
                }

                ModelEvent::remove(array_keys($existEvents));
            }
        }

        return true;
    }

    /**
     * Function save
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     *
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     *
     * @return bool
     */
    public function saveEvent(InterfaceEvent $event, VariablesInterface $eventData)
    {
        if($event->isEnabled()){
            if($event->beforeSaveEvent($this, $event, $eventData)){
                $this->_saveEvent($event, $eventData);
                $event->afterSaveEvent($this, $event, $eventData);
            }
        }

        return true;
    }

    /**
     * Function setEventAsHandled
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     * @param \Popuper\Event\InterfaceEvent $event
     *
     * @return \Popuper\EventsRepository
     */
    public function setEventAsHandled(InterfaceEvent $event, VariablesInterface $eventData)
    {
        if($event->isLoaded()){
            ModelEventsLog::save(
                [
                    'id' => $event->getId(),
                    'leadId' => $this->_leadId,
                    'typeId' => $this->_typeId,
                    'additionalValues' => $eventData->getValues(),
                ],
                ModelEventsLogStatuses::SHOWN,
                'Was shown.'
            );
        }

        if(!$event->isPermanent() && $event->getId()){
            ModelEvent::remove($event->getId());
            ModelEventsLog::save(
                [
                    'id' => $event->getId(),
                    'leadId' => $this->_leadId,
                    'typeId' => $this->_typeId,
                    'additionalValues' => $eventData->getValues(),
                ],
                ModelEventsLogStatuses::REMOVED,
                'Remove event due to showing of its popup.'
            );
        }

        return $this;
    }

    /**
     * Function alreadyExist
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param \Popuper\Event\InterfaceEvent $event
     *
     * @return bool
     */
    public function isEventAlreadyExist(InterfaceEvent $event)
    {
        if(
            !$event->isLoaded()
            || !$event->getId()
        ){
            return false;
        }

        $existEvents = ModelEvent::get($this->_leadId, $this->_typeId, true);

        return isset($existEvents[$event->getId()]);
    }

    /**
     * @return int|null
     */
    public function getLeadId()
    {
        return $this->_leadId;
    }

    /**
     * @param int|null $leadId
     *
     * @return EventsRepository
     */
    public function setLeadId($leadId)
    {
        $this->_leadId = $leadId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeId()
    {
        return $this->_typeId;
    }

    /**
     * @param int|null $typeId
     *
     * @return EventsRepository
     */
    public function setTypeId($typeId)
    {
        $this->_typeId = $typeId;

        return $this;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \Popuper\Variables\RequestData
     */
    public function getRequestData()
    {
        return new RequestData();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     *
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     */
    protected function _saveEvent(InterfaceEvent $event, VariablesInterface $eventData)
    {
        $typeIds = ModelEventType::getFieldByGroupId($event->getGroupId());
        $existEvents = ModelEvent::get($this->_leadId, $typeIds, true);

        if(
            (count($existEvents) == 1)
            && (Arr::get(current($existEvents), 'typeId') == $this->_typeId)
        ){
            $existEventId = key($existEvents);

            $status = ModelEventsLogStatuses::UPDATED;
            if(
                ($evId = $event->getId()) == $existEventId
            ){
                $reason = 'Update event due to data change.';
            } else {
                $reason = 'Update event due to replaced event.';
                $event->setId($existEventId);
            }
        } else {
            if(
                ($evId = $event->getId())
                && isset($existEvents[$evId])
            ){
                unset($existEvents[$evId]);
                $status = ModelEventsLogStatuses::UPDATED;
                $reason = 'Update event due to data change.';
            } else {
                $status = ModelEventsLogStatuses::ADDED;
                $reason = 'Add new event.';
            }

            if($existEvents){
                ModelEvent::remove(array_keys($existEvents));
                foreach ($existEvents as $eventId => $eventData) {
                    ModelEventsLog::save(
                        $eventData,
                        ModelEventsLogStatuses::REMOVED,
                        'Remove event due to another event with this type or group.'
                    );
                }
            }
        }

        switch ($status) {
            case ModelEventsLogStatuses::ADDED:
                $evId = ModelEvent::add(
                    $this->_leadId,
                    $this->_typeId,
                    $eventData->getValues()
                );
                $event->setId($evId);
                break;

            case ModelEventsLogStatuses::UPDATED:
                ModelEvent::update(
                    $event->getId(),
                    [
                        'leadId' => $this->_leadId,
                        'typeId' => $this->_typeId,
                        'additionalValues' => $eventData->getValues(),
                    ]
                );
                break;
        }

        $logData = [
            'id' => $evId,
            'leadId' => $this->_leadId,
            'typeId' => $this->_typeId,
            'additionalValues' => $eventData->getValues(),
        ];

        ModelEventsLog::save($logData, $status, $reason);
    }

}