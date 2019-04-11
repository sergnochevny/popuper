<?php

namespace Popuper\Event;

use Popuper\EventsRepository;
use Popuper\Model\EventType;
use Popuper\Model\EventVariable;
use Popuper\Variables\Dynamic\VariablesInterface;

/**
 * Class UnspecifiedPermanent
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Event
 */
class UnspecifiedPermanent extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_UNSPECIFIED_PERMANENT;

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
        if(!$eventData->getValue(EventVariable::CUSTOM_POPUP_IDS_LIST)){
            $eventsRepository->removeEvent($event, 'Remove event after trader clicked on "I Agree" button');

            return false;
        }

        return true;
    }

}