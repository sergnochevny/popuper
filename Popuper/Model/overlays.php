<?php
namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class Overlays
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class Overlays extends Model
{
    const TABLE_NAME = 'popupsOverlays';

    /**
     * Function getById
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
    public function getById($id)
    {
        if (!$id) {
            return [];
        }

        $cacheName = "popuper:::popupsOverlays:getById:id:{$id}";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select()
                ->from(self::TABLE_NAME)
                ->where('id', '=', $id)
                ->execute()
                ->current();

            $result = ($result) ? :[];

            Cache::instance()->set(
                $cacheName,
                $result,
                Kohana::config('global/cache.popuper.popupsOverlays')
            );

        }


        return $result;
    }
}
