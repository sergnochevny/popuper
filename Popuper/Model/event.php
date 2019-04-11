<?php
namespace Popuper\Model;

use Model;
use DB;

/**
 * Class Events
 *
 * @author Bogdan Medvedev <bogdan.medvedev@tstechpro.com>
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @property int id
 * @property int typeId
 * @property int leadId
 * @property string additionalValues
 *
 * @property EventType type
 *
 * @package Popuper\Model
 */
class Event extends Model
{

    const TABLE_NAME = 'popupEvents';

    /**
     * Function get
     * 
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array|int $leadIds
     * @param array $typeId
     * @param bool|null $isEnabled Filter by Enabled field.
     *      Null - to get all. Yes - to get enabled only, false  - to get disabled only
     *
     * @return array
     */
    public static function get($leadIds, $typeId = [], $isEnabled = null)
    {

        if (!is_array($leadIds)) {
            $leadIds = [$leadIds];
        }

        $forNonLoggedIn = false;
        foreach ($leadIds as $index => $leadId) {
            if (is_null($leadId)) {
                $forNonLoggedIn = true;
                unset($leadIds[$index]);
            }
        }

        if ($typeId && !is_array($typeId)) {
            $typeId = [$typeId];
        }

        $result = DB::select(
            'E.*',
            'ET.weight',
            'ET.isPermanent',
            'ET.enabled'
        )
            ->from([self::TABLE_NAME, 'E'])
            ->join([EventType::TABLE_NAME, 'ET'])
            ->on('E.typeId', '=', 'ET.id');


        $result->where_open();

            if ($leadIds) {
                $result->or_where('leadId', 'IN', $leadIds);
            }

            if ($forNonLoggedIn) {
                $result->or_where('leadId', 'IS', NULL);
            }

        $result->where_close();


        if ($typeId) {
            $result->where('typeId', 'IN', $typeId);
        }

        if (!is_null($isEnabled)) {
            $result->where('ET.enabled', '=', $isEnabled);

        }

        return $result
            ->order_by('weight', 'DESC')
            ->execute()
            ->as_array('id');

    }

    /**
     * Function add
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int|null $leadId
     * @param int $typeId
     * @param array $additionalValues
     *
     * @return mixed
     * @throws \Kohana_Exception
     */
    public static function add($leadId, $typeId, $additionalValues)
    {
        $data = [
            'leadId' => $leadId,
            'typeId' => $typeId,
            'additionalValues' => json_encode($additionalValues),
        ];

        $result = DB::insert(self::TABLE_NAME)
            ->columns(array_keys($data))
            ->values($data)
            ->execute();

        $allowedVariables = EventVariable::getAllowedForEvent($typeId);
        foreach ($additionalValues as $param => $value) {
            if (!isset($allowedVariables[$param])) {
                continue;
            }
            EventVariableValues::addOrReplace($allowedVariables[$param]['id'], $result[0], $value);
        }

        return $result[0];
    }

    /**
     * Function update
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $id
     *
     * @return bool
     */
    public static function update($id, $data)
    {
        $toSave = [];

        if (isset($data['leadId'])) {
            $toSave['leadId'] = $data['leadId'];
        }

        if (isset($data['typeId'])) {
            $toSave['typeId'] = $data['typeId'];
        }

        if (isset($data['additionalValues'])) {
            $toSave['additionalValues'] = json_encode($data['additionalValues']);
        }

        if (!$toSave) {
            return false;
        }

        DB::update(self::TABLE_NAME)
            ->set($toSave)
            ->where('id', '=', $id)
            ->execute();

        $allowedVariables = EventVariable::getAllowedForEvent($data['typeId']);
        foreach ($data['additionalValues'] as $param => $value) {
            if (!isset($allowedVariables[$param])) {
                continue;
            }
            EventVariableValues::addOrReplace($allowedVariables[$param]['id'], $id, $value);
        }

        return true;
    }

    /**
     * Function remove
     * BE CAREFUL! Can remove very important events!
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param mixed $ids
     * @param mixed $leadIds
     * @param mixed $typeIds
     *
     * @return bool
     */
    public static function remove($ids = [], $leadIds = [], $typeIds = [])
    {
        if (!$ids) {
            $ids = [];
        } elseif (!is_array($ids)) {
            $ids = [$ids];
        }

        if ($leadIds || $typeIds) {
            /** @var \Database_Query_Builder_Select $q */
            $query = DB::select('id')->from(self::TABLE_NAME);
            if ($leadIds) {
                $query->where('leadId', 'IN', $leadIds);
            }
            if ($typeIds) {
                $query->where('typeId', 'IN', $typeIds);
            }
            /** @var \Database_Result $result */
            $result = $query->execute();

            $ids = array_merge($ids, $result->as_array(null, 'id'));
        }

        if (!$ids) {
            return false;
        }

        $query = DB::delete(self::TABLE_NAME);

        $query->where('id', 'IN', $ids);

        $query->execute();

        EventVariableValues::deleteByEventIds($ids);

        return true;
    }

}
