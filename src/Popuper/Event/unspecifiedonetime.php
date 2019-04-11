<?php

namespace Popuper\Event;

use Popuper\Model\EventType;

/**
 * Class UnspecifiedPermanent
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class UnspecifiedOneTime extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_UNSPECIFIED_ONE_TIME;
}