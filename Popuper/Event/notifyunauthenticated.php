<?php

namespace Popuper\Event;

use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Model\EventType as ModelEventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Class LoginNotAllowedForUnauth
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper
 */
class NotifyUnauthenticated extends AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_NOTIFY_UNAUTHENTICATED;

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
        $eventData = $eventVariablesRepository->getEventData();
        $showInCountries = $eventData->getValue(EventVariable::COUNTRIES_LIST)
            ?: [];

        return (
            in_array($requestData->countryByIP, $showInCountries)
            && (
                !$requestData->lastNotifiedUnsupportedCountry
                || $requestData->lastNotifiedUnsupportedCountry != $requestData->countryByIP
            )
        );
    }

}