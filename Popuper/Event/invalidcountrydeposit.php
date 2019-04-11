<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class InvalidCountryDeposit
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class InvalidCountryDeposit extends AbstractEvent
{
    protected $_typeId = ModelEventType::EVENT_INVALID_COUNTRY_DEPOSIT;

}