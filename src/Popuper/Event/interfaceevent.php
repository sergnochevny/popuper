<?php

namespace Popuper\Event;

use Exception;
use Popuper\Builder\Repository as PopupBuilderRepository;
use Popuper\EventsRepository;
use Popuper\Variables\Dynamic\VariablesInterface;
use Popuper\Variables\Repository as EventVariablesRepository;
use Popuper\Variables\RequestData;

/**
 * Interface InterfaceEvent
 * @author  Andrey Fomov <andrey.fomov@tstechpro.com>
 * @package Popuper\Event
 */
interface InterfaceEvent
{
    /**
     * Function __construct
     * AbstractEvent constructor.
     * @throws Exception
     *
     * @param $leadId
     */
    public function __construct($leadId);

    /**
     * Function LeadId
     * Set _leadId value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int|null $leadId
     */
    public function setLeadId($leadId);

    /**
     * Function setId
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param $val
     *
     * @return void
     */
    public function setId($val);

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param RequestData $val
     */
    public function setRequestData(RequestData $val);

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return RequestData
     */
    public function getRequestData();

    /**
     * Function Enabled
     * Get _enabled value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isEnabled();

    /**
     * Function isPermanent
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return bool
     */
    public function isPermanent();

    /**
     * get event type id
     * @author Roman Lazarskiy <roman.lazarskiy@tstechpro.com>
     * @return int
     */
    public function getTypeId();

    /**
     * @return bool
     */
    public function isLoaded();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @return int
     */
    public function getWeight();

    /**
     * Function _checkShowRules
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
    );

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
    );

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     *
     * @return mixed
     */
    public function beforeSaveEvent(
        EventsRepository $eventsRepository,
        InterfaceEvent $event,
        VariablesInterface $eventData
    );

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\EventsRepository $eventsRepository
     * @param \Popuper\Event\InterfaceEvent $event
     * @param \Popuper\Variables\Dynamic\VariablesInterface $eventData
     *
     * @return mixed
     */
    public function afterSaveEvent(
        EventsRepository $eventsRepository,
        InterfaceEvent $event,
        VariablesInterface $eventData
    );

}