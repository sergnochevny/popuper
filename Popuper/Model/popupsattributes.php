<?php
namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupsAttributes
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsAttributes extends Model
{
    const TABLE_NAME = 'popupsAttributes';

    /**
     * Function getAll
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
    public function getAll($popupId)
    {
        if (!$popupId) {
            return [];
        }

        $cacheName = "popuper:::popupsAttributes:getAll:popupId:{$popupId}";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select()
                ->from(self::TABLE_NAME)
                ->where('popupId', '=', $popupId)
                ->execute()
                ->as_array('name', 'value');

            Cache::instance()->set(
                $cacheName,
                $result,
                Kohana::config('global/cache.popuper.popupsAttributes')
            );

        }

        return $result;
    }

    /**
     * Function save
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $attributes
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public function save($popupId, array $attributes)
    {
        $exist = $this->getAll($popupId);
        ksort($exist);
        ksort($attributes);

        if (!$attributes || $attributes == $exist) {
            return false;
        }

        $insert = [];
        $update = [];
        $delete = [];

        foreach ($attributes as $name => $value) {

            if (!isset($exist[$name])) {
                if ($value) {
                    $insert[$name] = $value;
                }
                continue;
            }

            if ($exist[$name] == $value) {
                continue;
            }

            if ($value) {
                $update[$name] = $value;
            } else {
                $delete[] = $name;
            }

        }

        if (!$insert && !$update && !$delete) {
            return false;
        }

        return $this->_insertForPopupByNames($popupId, $insert)
               || $this->_updateForPopupByNames($popupId, $update)
               || $this->_deleteByNames($popupId, $delete);
    }

    /**
     * Function _insertForPopupByNames
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $insertData
     *
     * @return bool
     * @throws \Kohana_Exception
     */
    protected function _insertForPopupByNames($popupId, array $insertData)
    {

        if (!$insertData) {
            return false;
        }

        $query = DB::insert(self::TABLE_NAME)
            ->columns(
                [
                    'popupId',
                    'name',
                    'value',
                ]
            );
        foreach ($insertData as $name => $value) {

            $query->values(
                [
                    $popupId,
                    $name,
                    $value,
                ]
            );
        }

        $query->execute();

        return true;
    }

    /**
     * Function _updateForPopupByNames
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $updateData
     *
     * @return int|object
     */
    protected function _updateForPopupByNames($popupId, array $updateData)
    {

        $updatedCount = 0;
        foreach ($updateData as $name => $value) {
            $updatedCount += DB::update(self::TABLE_NAME)
                ->set(['value' => $value])
                ->where('popupId', '=', $popupId)
                ->where('name', '=', $name)
                ->execute();
        }

        return $updatedCount;
    }

    /**
     * Function _deleteByNames
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $names
     *
     * @return bool
     */
    protected function _deleteByNames($popupId, array $names)
    {

        if (!$names) {
            return false;
        }

        DB::delete(self::TABLE_NAME)
            ->where('popupId', '=', $popupId)
            ->where('name', 'in', $names)
            ->execute();

        return true;
    }
}
