<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class DepositedNotVerified
 *
 * @author Alexander Shpak <alexander.shpak@tstechpro.com>
 */
class DepositedNotVerified extends AbstractEvent
{
    /** @var int */
    protected $_typeId = ModelEventType::EVENT_DEPOSITED_NOT_VERIFIED;
}