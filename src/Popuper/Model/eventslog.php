<?php
namespace Popuper\Model;

use Arr;
use Model;
use DB;

/**
 * Class EventsLog
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class EventsLog extends Model
{

    const TABLE_NAME = 'popupEventsLog';

    /**
     * Function save
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $eventData
     * @param $status
     * @param string $reason
     *
     * @return bool
     * @throws \Kohana_Exception
     */
    public static function save($eventData, $status, $reason = '')
    {
        $additionalValues = Arr::get($eventData, 'additionalValues', []);

        $data = [
            'eventId' => Arr::get($eventData, 'id'),
            'typeId' => Arr::get($eventData, 'typeId'),
            'leadId' => Arr::get($eventData, 'leadId'),
            'additionalValues' => (is_array($additionalValues)) ? json_encode($additionalValues) : $additionalValues,
            'statusId' => $status,
            'reason' => $reason,
            'date' => DB::expr('NOW()'),
        ];

        DB::insert(self::TABLE_NAME)
            ->columns(array_keys($data))
            ->values($data)
            ->execute();

        return true;

    }
}