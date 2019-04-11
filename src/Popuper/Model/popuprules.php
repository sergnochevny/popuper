<?php
namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupRules
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupRules extends Model
{
    const TABLE_NAME = 'popupsRules';

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

        $cacheName = "popuper:::popupsRules:getByPopupId:popupId:{$popupId}";

        $result = Cache::instance()->get($cacheName);
        if (!is_null($result)) {

            return $result;
        }

        $result = DB::select('id','ruleData')
            ->from(self::TABLE_NAME)
            ->where('popupId', '=', $popupId)
            ->where('v', '=', 1)
            ->order_by('id', 'ASC')
            ->execute()
            ->as_array('id','ruleData');

        Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popupsRules'));

        return $result;
    }
}
