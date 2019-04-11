<?php
namespace Popuper\Model;

use Model;

/**
 * Class EventsLog
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class EventsLogStatuses extends Model
{

    const TABLE_NAME = 'popupEventsLogStatuses';

    const ADDED = 1;
    const UPDATED = 2;
    const REMOVED = 3;
    const SHOWN = 4;
}