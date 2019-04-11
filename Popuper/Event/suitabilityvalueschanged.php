<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * @deprecated
 */
class SuitabilityValuesChanged extends  AbstractEvent
{

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_SUITABILITY_VALUES_CHANGED;

}