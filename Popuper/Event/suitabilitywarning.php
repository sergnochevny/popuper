<?php

namespace Popuper\Event;

use Arr;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Class Suspense
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class SuitabilityWarning extends  AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_SUITABILITY_WARNING;

    /** @var string */
    protected $_pageId = 'survey';

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
        if (
            $this->_leadId
            && !$eventData->getValue(EventVariable::IS_SURVEY_COMPLETE_FOR_VERIFICATION)
        ) {
            $eventsRepository->removeEvent(
                $event,
                'Remove event due to questionnaire submission'
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
        /** Popups are available on Survey page ONLY if no validation errors found */
        return !$requestData->surveySubmitFailed && ($requestData->forMobile || $this->_pageId == $requestData->pageId);
    }
}