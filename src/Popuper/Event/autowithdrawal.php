<?php

namespace Popuper\Event;

use Popuper\Model\EventType;

/**
 * Class AutoWithdrawal
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 * @package Popuper\Event
 */
class AutoWithdrawal extends AbstractEvent
{
    /** @var int */
    protected $_typeId = EventType::EVENT_AUTO_WITHDRAWAL;

}