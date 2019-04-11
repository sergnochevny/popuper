<?php

namespace Popuper\Event;

use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Class QuizResultsMaxAttempts
 *
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class QuizResultsMaxAttempts extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_QUIZ_RESULTS_MAX_ATTEMPTS;

    /** @var string */
    protected $_pageId = 'quiz';

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
        /** Popups are NOT available on Terms&Conditions page */
        return $requestData->forMobile || $this->_pageId == $requestData->pageId;
    }
}