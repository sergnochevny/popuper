<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class WrongRegion
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class WrongRegion extends  AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_WRONG_REGION;

}