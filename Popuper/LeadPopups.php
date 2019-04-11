<?php

namespace Popuper;

use Arr;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\Event\AbstractEvent;
use Popuper\Event\InterfaceEvent;
use Popuper\Model\Event;
use Popuper\Variables\Repository as EventVariablesRepository;
use SystemOptions;

/**
 * Class LeadPopups
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper
 */
class LeadPopups extends EventsManager
{
    /**
     * @var \Popuper\Builder\Repository
     */
    protected $_popupBuilderRepository;

    /**
     * LeadPopups constructor.
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Variables\Repository $eventVariablesRepository
     * @param \Popuper\Builder\Repository $popupBuilderRepository
     */
    public function __construct(
        EventsRepository $eventsRepository,
        EventVariablesRepository $eventVariablesRepository,
        PopupBuilderRepository $popupBuilderRepository
    ) {
        parent::__construct($eventsRepository, $eventVariablesRepository);

        $this->_popupBuilderRepository = $popupBuilderRepository;
    }

    /**
     * Function isEnabled
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return bool
     */
    public static function isEnabled()
    {
        return SystemOptions::get('Pop-uper//Pop-uper Enabled');
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\Event\InterfaceEvent $event
     *
     * @return bool
     */
    public function hasEventAllowedToShowPopup(InterfaceEvent $event)
    {
        return $event->isAllowedToShowPopups(
            $this->_eventsRepository,
            $this->_eventVariablesRepository,
            $this->_popupBuilderRepository
        );
    }

    /**
     * Function getData
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     * @return array
     */
    public function getData()
    {
        $result = [
            'html' => '',
            'typeId' => 0,
            'js' => '',
            'settings' => [],
            'scripts' => [],
            'styles' => [],
        ];

        /** @var AbstractEvent $event */
        if($event = $this->getEvent()){
            /** @var array $popupData */
            $result = Arr::overwrite(
                $result,
                $this->_popupBuilderRepository
                    ->setEvent($event)
                    ->setEventVariables(
                        $this->_eventVariablesRepository->getDataForReplace()
                    )
                    ->getPopupData()
            );
        }

        return $result;
    }

    /**
     * Function popupShowed
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return \Popuper\LeadPopups
     */
    public function setPopupShowed()
    {
        return $this->setCurrentEventAsHandled();
    }

    /**
     * Function _findNextPopupEvent
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     * @return \Popuper\Event\AbstractEvent
     */
    protected function _findNextEvent()
    {
        $popupEvent = null;

        if(static::isEnabled()){
            return $popupEvent;
        }

        if(!$leadId = $this->_eventsRepository->getLeadId()){
            $leadIds = array_unique(
                [
                    $this->getRequestData()->notAuthedUID,
                    null,
                ]
            );
        } else {
            $leadIds = [$leadId];
        }

        foreach ($leadIds as $leadId) {
            $events = Event::get($leadId, $this->_eventsRepository->getTypeId(), true);
            /** @var [id, typeId, leadId, additionalValues] $eventData */
            foreach ($events as $eventData) {
                $eventTypeId = $eventData['typeId'];
                if(in_array($eventTypeId, $this->getRequestData()->excludedTypes)){
                    continue;
                }

                $this->_eventsRepository->setTypeId($eventTypeId);
                $this->resetEvent();
                $event = parent::getEvent();

                if(
                    $this->hasEventAllowedToShowPopup($event)
                    && $this->_popupBuilderRepository
                        ->setEvent($event)
                        ->hasValidPopup()
                ){
                    $popupEvent = $event;

                    break;
                }
            }
            if($popupEvent !== null){
                break;
            }
        }

        return $popupEvent;
    }

}