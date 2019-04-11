<?php
namespace Popuper\Model;

use Model;

/**
 * Class EventVariableValues
 *
 *
 * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 *
 * @package Popuper\Model
 */
class EventVariableValues extends Model
{
    const TABLE_NAME = 'popupEventVariableValues';

    /**
     * Method creates of replaces values.
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param int $popupEventVariableId
     * @param int $popupEventId
     * @param string|int|array $value
     * @throws \Kohana_Exception
     */
    public static function addOrReplace($popupEventVariableId, $popupEventId , $value)
    {
        \DB::delete(self::TABLE_NAME)
            ->where('popupEventVariableId', '=', $popupEventVariableId)
            ->where('popupEventId', '=', $popupEventId)
            ->execute();

        if (!empty($value)) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $insertQuery = \DB::insert(self::TABLE_NAME, ['popupEventVariableId', 'popupEventId', 'value']);
            foreach ($value as $valueItem) {
                $insertQuery->values([$popupEventVariableId, $popupEventId, $valueItem]);
            }
            $insertQuery->execute();
        }
    }

    /**
     * Method gives values of the event.
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param $eventId
     * @return mixed
     */
    public static function getValues($eventId)
    {
        return \DB::select('v.systemName', 'ev.value', 'v.isMultiVal')
            ->from([self::TABLE_NAME, 'ev'])
            ->join([EventVariable::TABLE_NAME, 'v'])
            ->on('ev.popupEventVariableId', '=', 'v.id')
            ->where('ev.popupEventId', '=', $eventId)
            ->execute()
            ->as_array();
    }

    /**
     * Method deletes all values for the event.
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param array $eventIds
     */
    public static function deleteByEventIds(array $eventIds)
    {
        if (empty($eventIds)) {
            return;
        }
        $query = \DB::delete(self::TABLE_NAME);

        if (!is_array($eventIds)) {
            $eventIds = [$eventIds];
        }
        $query->where('popupEventId', 'IN', $eventIds);
        $query->execute();

    }
}
