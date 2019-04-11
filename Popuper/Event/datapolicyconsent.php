<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class DataPolicyConsent
 * @package Popuper\Event
 */
class DataPolicyConsent extends AbstractEvent
{
    /** @var int  */
    protected $_typeId = ModelEventType::EVENT_DATA_POLICY_CONSENT;
}
