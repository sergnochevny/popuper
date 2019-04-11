<?php

namespace Popuper\Model;

use DB;
use Model;

/**
 * Class EventVariable
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Model
 */
class EventVariable extends Model
{
    const TABLE_NAME = 'popupEventVariables';

    const TYPE_SINGLE_VAL = 0;
    const TYPE_MULTIPLE_VAL = 1;

    const TYPE_IS_SYSTEM = 1;
    const TYPE_IS_USER = 0;

    const COUNTRY_TYPE = 'COUNTRY_TYPE';
    const LEAD_BALANCE = 'LEAD_BALANCE';
    const IS_FIRST_SUITABILITY_CALCULATION = 'IS_FIRST_SUITABILITY_CALCULATION';
    const LEAD_LEVERAGE = 'LEAD_LEVERAGE';
    const EMAIL_VERIFICATION_STATUS = 'EMAIL_VERIFICATION_STATUS';
    const LEAD_TC_STATUS_ID = 'LEAD_TC_STATUS_ID';
    const PURPOSED_LEVERAGE_WAS_DECLINED = 'PURPOSED_LEVERAGE_WAS_DECLINED';
    const COUNTRY = 'COUNTRY';
    const SUSPENSION_REASON_ID = 'SUSPENSION_REASON_ID';
    const LEAD_CAN_ACCEPT_TC = 'LEAD_CAN_ACCEPT_TC';
    const LEAD_SUSPEND_STATUSES_LIST = 'LEAD_SUSPEND_STATUSES_LIST';
    const COUNTRIES_LIST = 'COUNTRIES_LIST';
    const LEVERAGE_CAN_INCREASE = 'LEVERAGE_CAN_INCREASE';
    const LEVERAGE_INCREASING_APPROVED = 'LEVERAGE_INCREASING_APPROVED';
    const CUSTOM_POPUP_IDS_LIST = 'CUSTOM_POPUP_IDS_LIST';
    const PREVIEW_POPUP_IDS_LIST = 'PREVIEW_POPUP_IDS_LIST';
    const AVAILABLE_LEAD_LEVERAGES = 'AVAILABLE_LEAD_LEVERAGES';
    const LEAD_SUITABILITY_LVL_ID = 'LEAD_SUITABILITY_LVL_ID';
    const LEAD_LEVERAGE_PREV = 'LEAD_LEVERAGE_PREV';
    const AVAILABLE_LEAD_LEVERAGES_PREV = 'AVAILABLE_LEAD_LEVERAGES_PREV';
    const LEAD_SUITABILITY_LVL_ID_PREV = 'LEAD_SUITABILITY_LVL_ID_PREV';
    const IS_SURVEY_COMPLETE_FOR_VERIFICATION = 'IS_SURVEY_COMPLETE_FOR_VERIFICATION';
    const SUITABILITY_VALUES_CHANGED = 'IS_SUITABILITY_VALUES_CHANGED';
    const LEAD_MAX_LEVERAGE = 'LEAD_MAX_LEVERAGE';

    /**
     * @param $systemName
     *
     * @return mixed
     */
    public static function findOneBySystemName($systemName)
    {
        /** @var \Database_Result $result */
        $result = DB::select()
            ->from(self::TABLE_NAME)
            ->where('systemName', '=', $systemName)
            ->execute();

        return $result->current();
    }

    /**
     * @throws \Kohana_Exception
     *
     * @param int $type
     * @param $systemName
     *
     * @return mixed
     */
    public static function findOneOrCreate($systemName, $type = self::TYPE_SINGLE_VAL)
    {
        /** @var \Database_Result $result */
        $result = DB::select()
            ->from(self::TABLE_NAME)
            ->where('systemName', '=', $systemName)
            ->execute();

        if(!$result->count()){
            DB::insert(self::TABLE_NAME, ['systemName', 'isMultiVal'])
                ->values([$systemName, $type])
                ->execute();

            /** @var \Database_Result $result */
            $result = DB::select()
                ->from(self::TABLE_NAME)
                ->where('systemName', '=', $systemName)
                ->execute();
        }

        return $result->current();
    }

    /**
     * @param integer $eventTypeId
     *
     * @return array
     */
    public static function getAllowedForEvent($eventTypeId)
    {
        return DB::select('v.*')
            ->from([EventVariable::TABLE_NAME, 'v'])
            ->join([EventVariableEventType::TABLE_NAME, 'vt'])
            ->on('v.id', '=', 'vt.popupEventVariableId')
            ->where('vt.popupEventTypeId', '=', $eventTypeId)
            ->execute()
            ->as_array('systemName');
    }

}
