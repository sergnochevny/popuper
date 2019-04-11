<?php
namespace Popuper\Model;

use Arr;
use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class EventTypes
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @property int id
 * @property string name
 * @property int isPermanent
 * @property int weight
 *
 * @property Event events
 *
 * @package Popuper\Model
 */
class EventType extends Model
{

    const EVENT_NOTIFY_UNAUTHENTICATED = 1;
    const EVENT_WRONG_REGION = 2;
    const EVENT_RE_ACCEPT_TERMS = 3;
    const EVENT_SITE_SURFING_FORBID = 4;
    const EVENT_SURVEY_INCOMPLETE = 5;
    const EVENT_SUITABILITY_WARNING = 7;
    const EVENT_INVALID_COUNTRY_DEPOSIT = 8;
    const EVENT_HOW_TO_TRADE = 10;
    const EVENT_QUIZ_RESULTS_INCOMPLETE = 11;
    const EVENT_QUIZ_RESULTS_COMPLETE = 12;
    const EVENT_QUIZ_RESULTS_MAX_ATTEMPTS = 13;
    const EVENT_AUTOVERIFICATION_COMPLETE = 14;
    const EVENT_DEPOSITED_NOT_VERIFIED = 16;
    const EVENT_UNSPECIFIED_PERMANENT = 17;
    const EVENT_UNSPECIFIED_ONE_TIME = 18;
    const EVENT_EMAIL_ADDRESS_VERIFICATION = 19;
    const EVENT_DATA_POLICY_CONSENT = 20;
    const EVENT_AUTO_WITHDRAWAL = 21;
    const EVENT_NOTIFY_UNAUTHENTICATED_FROM_UNSUPPORTED_COUNTRIES_BY_IP = 22;
    const EVENT_SUITABILITY_CALCULATION = 23;

    /** will be removed soon */
    const EVENT_SUITABILITY_VALUES_CHANGED = 6;
    const EVENT_SUITABILITY_VALUES_INCREASE = 9;
    const EVENT_UNSUITABLE_WARNING = 15;

    const TABLE_NAME = 'popupEventTypes';

    protected static $_unsortablePopupsEvents = [];

    /**
     * Function getAll
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @param bool|null $isEnabled Filter by Enabled field.
     *      Null - to get all. Yes - to get enabled only, false  - to get disabled only
     *
     * @return array
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public static function getAll($isEnabled = null)
    {

        $cacheName = 'popuper:::EventType:'
                     . (
                     (is_null($isEnabled))
                         ? 'all'
                         : ('enabled:' . (int) $isEnabled)
                     );

        /** @var array $result */
        $result = Cache::instance()->get($cacheName);
        if (is_null($result)) {

            $query = DB::select()
                ->from(self::TABLE_NAME);

            if (!is_null($isEnabled)) {
                $query->where('enabled', '=', $isEnabled);
            }

            /** @var array $result */
            $result = $query->execute()->as_array('id');

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popupEventTypes'));
        }

        return $result;

    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     * @throws \Kohana_Cache_Exception
     */
    public static function getAllForViewPrepared(){
        $result = [];
        $eventTypes = static::getAll();
        foreach ($eventTypes as $eventType){
            $eventType['isPermanent'] = (bool)$eventType['isPermanent'];
            $eventType['enabled'] = (bool)$eventType['enabled'];
            $result[] = $eventType; 
        }
        
        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return bool
     */
    public static function getSortablePopupsToken($eventTypeId){
        $result = true;
        if($eventTypeId && (in_array($eventTypeId, static::$_unsortablePopupsEvents))){
            $result = false;
        }
        
        return $result;
    }

    /**
     * Function get
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $id
     *
     * @return array|mixed
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public static function get($id)
    {

        return Arr::get(static::getAll(), $id, []);
    }

    /**
     * Function getFieldByGroupId
     *
     *
     * Get event types field (default is id) by group id
     *
     * @author Anatolii Lishchynskyi <anatolii.lishchynsky@tstechpro.com>
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int $groupId
     * @param string $field
     *
     * @return array
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public static function getFieldByGroupId($groupId, $field = 'id')
    {

        $cacheName = "popuper:::EventType:getFieldByGroupId:groupId:{$groupId}"
                     . (
                         ($field)
                             ? ":field:{$field}"
                             : ""
                     );

        /** @var array $result */
        $result = Cache::instance()->get($cacheName);
        if (is_null($result)) {

            /** @var array $result */
            $result = DB::select($field)
                ->from(self::TABLE_NAME)
                ->where('groupId', '=', $groupId)
                ->execute()
                ->as_array(null, $field);

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popupEventTypes'));
        }

        return $result;
    }
}