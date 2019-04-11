<?php

namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupsForEventTypes
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class Popups extends Model
{
    const TABLE_NAME = 'popups';

    /**
     * Function getById
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     *
     * @return array|mixed
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public function getById($popupId)
    {

        if (!$popupId) {
            return [];
        }

        $cacheName = "popuper:::popups:getById:id:{$popupId}";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select()
                ->from([self::TABLE_NAME, 'P'])
                ->where('id', '=', $popupId)
                ->execute()
                ->current();

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popups'));

        }

        return $result;
    }

    /**
     * Function getById
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $id
     *
     * @return int|null Id of saved popup
     * @throws \Exception
     * @throws \Kohana_Cache_Exception
     */
    public function set($id, array $data)
    {
        $availableFields = [
            'name',
            'templateId',
            'isActive',
        ];
        $exist = $this->getById($id);
        ksort($exist);
        $data = array_intersect_key($data, array_fill_keys($availableFields, ''));
        ksort($data);

        if (!$data || $data == $exist) {
            return null;
        }

        if (!$exist) {
            $inserResult = DB::insert(self::TABLE_NAME)
                ->columns(array_keys($data))
                ->values($data)
                ->execute();

            list($id,) = $inserResult;
        } else {
            DB::update(self::TABLE_NAME)
                ->set($data)
                ->where('id', '=', $id)
                ->execute();
        }

        return $id;
    }

    /**
     * Function getByIdsArray
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $popupIds
     * @param bool|null $isActive
     *
     * @return array|mixed
     * @throws \Kohana_Cache_Exception
     */
    public function getByIdsArray(array $popupIds = [], $isActive = null)
    {
        $cacheName = "popuper:::popups:getByIdsArray";
        if ($popupIds) {
            $cacheName .= ":ids:" . implode(',', $popupIds);
        }
        if (!is_null($isActive)) {
            $cacheName .= ":isActive:" . (int) $isActive;
        }

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

             $query = DB::select()
                ->from([self::TABLE_NAME, 'P']);

            if ($popupIds) {
                $query->where('id', 'IN', $popupIds);
            }

            if (!is_null($isActive)) {
                $query->where('isActive', '=', $isActive);
            }

            $result = $query->execute()->as_array('id');
            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popups'));

        }

        return $result;
    }

    /**
     * @param string $sortField
     * @param string $sortDir
     *
     * @return mixed
     * @throws \Kohana_Cache_Exception
     */
    public static function getWithTemplate($sortField = 'id', $sortDir = 'ASC')
    {
        $cacheName = "popuper:::popups:getWithTemplate:sort:{$sortField}:{$sortDir}";
        $result = Cache::instance()->get($cacheName);

        if ($result === null) {
            $result = DB::select(
                'P.id',
                'P.name',
                'P.isActive',
                ['PT.name', 'templateName'],
                ['PT.class', 'templateClass'],
                'PT.overlayId'
            )
                ->from([self::TABLE_NAME, 'P'])
                ->join([Templates::TABLE_NAME, 'PT'], 'LEFT')
                ->on('P.templateId', '=', 'PT.id')
                ->order_by($sortField, $sortDir)
                ->execute()
                ->as_array('id');

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popups'));
        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return mixed
     * @throws \Kohana_Cache_Exception
     */
    public static function getWithTemplateByEventType($eventTypeId)
    {
        $cacheName = "popuper:::popups:getWithTemplateByEventType:{$eventTypeId}";
        $result = Cache::instance()->get($cacheName);
        if ($result === null) {
            $result = DB::select(
                'P.id',
                'P.name',
                'P.isActive',
                ['PT.name', 'templateName'],
                ['PT.class', 'templateClass'],
                'PT.overlayId',
                'PET.order'
            )
                ->from([self::TABLE_NAME, 'P'])
                ->join([PopupsForEventTypes::TABLE_NAME, 'PET'], 'LEFT')
                ->on('P.id', '=', 'PET.popupId')
                ->join([Templates::TABLE_NAME, 'PT'], 'LEFT')
                ->on('P.templateId', '=', 'PT.id')
                ->where('PET.eventTypeId', '=', DB::expr($eventTypeId))
                ->order_by('PET.order', 'ASC')
                ->order_by('P.id', 'ASC')
                ->execute()
                ->as_array('id');
            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popups'));

        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $popupsData
     *
     * @param array $petData
     *
     * @return mixed
     * @throws \Database_TransactionException
     * @throws \Kohana_Cache_Exception
     */
    public static function savePopupsByEventType(array $popupsData, array $petData)
    {
        $result = false;

        DB::startTransaction();

        foreach ($popupsData as $popupId => $popupData) {
            $affectedRows = DB::update(static::TABLE_NAME)
                ->set($popupData)
                ->where('id', '=', $popupId)
                ->execute();
            $result = $affectedRows >= 0;
            
            $affectedRows = DB::update(PopupsForEventTypes::TABLE_NAME)
                ->set($petData[$popupId])
                ->where('popupId', '=', $popupId)
                ->execute();
            $result = $result && ($affectedRows >= 0);
            
            if (!$result) {
                break;
            }
        }

        if ($result) {
            DB::commit();
            /** clear whole popuper cache */
            Cache::instance()->removeAll("popuper:::");
        } else {
            DB::rollback();
        }

        return $result;
    }

    /**
     * @param array|null $conditions
     *
     * @return mixed
     * @throws \Kohana_Cache_Exception
     * @throws \Exception
     */
    public static function getByCondition(array $conditions = null)
    {
        $cacheName = 'popuper:::popups:getByCondition:::' . json_encode($conditions);
        $result = Cache::instance()->get($cacheName);

        if ($result === null) {
            $query = DB::select()
                ->from(self::TABLE_NAME);

            if (!empty($conditions)) {
                foreach ($conditions as $condition) {
                    $query->where(...$condition);
                }
            }

            $result = $query->execute()->as_array();

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popups'));
        }

        return $result;
    }

}
