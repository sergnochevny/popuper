<?php
namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupsScripts
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsScripts extends Model
{
    const TABLE_NAME = 'popupsScripts';

    /**
     * Function getByPopupId
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
    public function getByPopupId($popupId)
    {

        if (!$popupId) {
            return [];
        }

        $cacheName = "popuper:::popupsScripts:getByPopupId:{$popupId}";
        $result = Cache::instance()->get($cacheName);
        if (is_null($result)) {

            $result = DB::select()
                ->from([self::TABLE_NAME, 'ET'])
                ->where('popupId', '=', $popupId)
                ->order_by('order', 'ASC')
                ->execute()
                ->as_array('order', 'address');

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popupsScripts'));
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
     * @param array $list
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public function save($popupId, array $list)
    {
        $exist = $this->getByPopupId($popupId);
        ksort($exist);
        ksort($list);

        if ($list == $exist) {
            return false;
        }


        $deleted = $this->_deleteAllForPopup($popupId);
        $inserted = $this->_insertForPopupByOrders($popupId, $list);

        return $deleted || $inserted;
    }

    /**
     * Function _insertForPopupByOrders
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
    protected function _insertForPopupByOrders($popupId, array $insertData)
    {

        if (!$insertData) {
            return false;
        }

        $query = DB::insert(self::TABLE_NAME)
            ->columns(
                [
                    'popupId',
                    'order',
                    'address',
                ]
            );
        foreach ($insertData as $order => $address) {

            $query->values(
                [
                    $popupId,
                    $order,
                    $address,
                ]
            );
        }

        $query->execute();

        return true;
    }

    /**
     * Function _deleteAllForPopup
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     */
    protected function _deleteAllForPopup($popupId)
    {

        if (!$popupId || !$this->getByPopupId($popupId)) {
            return false;
        }

        DB::delete(self::TABLE_NAME)
            ->where('popupId', '=', $popupId)
            ->execute();

        return true;
    }
}
