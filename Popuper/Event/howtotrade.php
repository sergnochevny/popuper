<?php

namespace Popuper\Event;

use Popuper\Model\EventType;

/**
 * Class HowToTrade
 *
 *
 * @author Denis Chunyak <denis.chunyak@tstechpro.com>
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class HowToTrade extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_HOW_TO_TRADE;
}