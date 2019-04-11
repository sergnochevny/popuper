<?php

namespace Popuper\Event;

use Arr;
use Leads;
use Model_LeadsTermsAcceptanceStatuses;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Class ReAcceptTerms
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Event
 */
class ReAcceptTerms extends AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_RE_ACCEPT_TERMS;

    /** @var string */
    protected $_pageId = 'terms';

    /**
     * Function save
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     * @param \Popuper\EventsRepository $eventsRepository
     *
     * @return bool
     */
    public function beforeSaveEvent(
        EventsRepository $eventsRepository,
        InterfaceEvent $event,
        VariablesInterface $eventData
    ) {
        if(
            $this->_leadId
            && !$eventData->getValue(EventVariable::COUNTRY_TYPE)
        ){
            $leadsData = Leads::get_lead_info($this->_leadId);
            $eventData->setValue(
                EventVariable::COUNTRY_TYPE,
                Arr::get($leadsData, 'country_type')
            );
        }

        if(
            $this->_leadId
            && (
                $eventData->getValue(EventVariable::LEAD_TC_STATUS_ID) ==
                Model_LeadsTermsAcceptanceStatuses::AGREED
            )
        ){
            $eventsRepository->removeEvent(
                $event,
                'Remove event due to lead T&C acceptance.'
            );

            return false;
        }

        return true;
    }

    /**
     * Function _checkShowRules
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
        $requestData = $eventVariablesRepository->getRequestData();

        /** Popups are NOT available on Terms&Conditions page */
        return $requestData->forMobile || $this->_pageId != $requestData->pageId;
    }

}