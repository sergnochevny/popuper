<?php

namespace Popuper\Event;

use Model_LeadsSuspendReason;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Class Suspense
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Event
 */
class SurveyIncomplete extends AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_SURVEY_INCOMPLETE;

    /** @var string */
    protected $_pageId = 'survey';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Kohana_Exception
     *
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     * @param \Popuper\EventsRepository $eventsRepository
     *
     * @return bool|mixed
     */
    public function beforeSaveEvent(
        EventsRepository $eventsRepository,
        InterfaceEvent $event,
        VariablesInterface $eventData
    ) {
        if(
            $this->_leadId
            && !in_array(
                Model_LeadsSuspendReason::REASON_QUESTIONNAIRE_IS_NOT_COMPLETE,
                ($eventData->getValue(EventVariable::LEAD_SUSPEND_STATUSES_LIST)
                    ?: []
                )
            )
        ){
            $eventsRepository->removeEvent(
                $event,
                'Remove event due to lead unsuspend for reason "Auto suspended until questionnaire is complete"'
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
    ){
        $requestData = $eventVariablesRepository->getRequestData();

        /** Popups ARE NOT available on Survey page for NON-mobile devices */
        return !$requestData->forMobile || $this->_pageId != $requestData->pageId;
    }

}