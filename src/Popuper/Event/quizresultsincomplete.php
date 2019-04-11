<?php

namespace Popuper\Event;

use Popuper\Model\EventType;

/**
 * Class QuizResultsIncomplete
 *
 * @author Alexandr Sarapuka <alexandr.sarapuka@tstechpro.com>
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Event
 */
class QuizResultsIncomplete extends AbstractEvent
{
    /** @var int|null */
    protected $_typeId = EventType::EVENT_QUIZ_RESULTS_INCOMPLETE;
}