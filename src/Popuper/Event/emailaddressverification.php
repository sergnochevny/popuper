<?php

namespace Popuper\Event;

use Popuper\Model\EventType as ModelEventType;

/**
 * Class EmailAddressVerification
 * @package Popuper\Event
 * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
 */
class EmailAddressVerification extends AbstractEvent
{
    /** @var int */
    protected $_typeId = ModelEventType::EVENT_EMAIL_ADDRESS_VERIFICATION;
}