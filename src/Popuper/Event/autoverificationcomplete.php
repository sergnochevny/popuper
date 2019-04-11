<?php

namespace Popuper\Event;

use Popuper\Model\EventType;

/**
 * Class AutoVerificationComplete
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class AutoVerificationComplete extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_AUTOVERIFICATION_COMPLETE;

}