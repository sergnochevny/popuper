<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * @deprecated
 */
class UnsuitableWarning extends AbstractEvent
{

    /** @var int */
    protected $_typeId = ModelEventType::EVENT_UNSUITABLE_WARNING;

}