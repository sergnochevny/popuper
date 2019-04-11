<?php

namespace Popuper\Model;

use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class EventTypeGroups
 *
 * @property int id
 * @property string name
 * @property string description
 *
 * @package Popuper\Model
 */
class EventGroups extends Model
{
    const TABLE_NAME = 'popupEventGroups';

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @return array
     * @throws \Kohana_Cache_Exception
     */
    public static function getAll()
    {
        $cacheName = 'popuper:::EventTypeGroups:all';
        /** @var array $result */
        $result = Cache::instance()->get($cacheName);
        if ($result === null) {

            $query = DB::select()
                ->from(self::TABLE_NAME);

            /** @var array $result */
            $result = $query->execute()->as_array('id');

            Cache::instance()->set($cacheName, $result, Kohana::config('global/cache.popuper.popupEventTypes'));
        }

        return $result;
    }

}