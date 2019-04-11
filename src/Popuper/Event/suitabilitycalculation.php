<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class SuitabilityCalculation
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class SuitabilityCalculation extends  AbstractEvent
{
    const INCREASE_APPROVED = 1;
    const INCREASE_NOT_APPROVED = -1;

    /** @var int|null */
    protected $_typeId = ModelEventType::EVENT_SUITABILITY_CALCULATION;

}