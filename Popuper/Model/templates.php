<?php
namespace Popuper\Model;

use Arr;
use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupsTemplates
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class Templates extends Model
{
    const TABLE_NAME = 'popupsTemplates';

    const ID_CENTER_OVERLAY_NON_CLOSABLE = 1;
    const ID_CENTER_OVERLAY_CLOSABLE = 2;
    const ID_CENTER_OVERLAY_CLOSE_WITH_BTN_ONLY = 3;
    const ID_BOTTOM_FULL_CLICK_TO_CLOSE = 4;
    const ID_TOP_FULL_CLICK_TO_CLOSE = 5;
    const ID_TOP_LEFT_CLICK_TO_CLOSE = 6;
    const ID_TOP_RIGHT_CLICK_TO_CLOSE = 7;
    const ID_BOTTOM_LEFT_CLICK_TO_CLOSE = 8;
    const ID_BOTTOM_RIGHT_CLICK_TO_CLOSE = 9;

    /**
     * Function getAll
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return array
     * @throws \Kohana_Cache_Exception
     */
    public function getAll()
    {

        $cacheName = "popuper:::popupsTemplates:getAll";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select()
                ->from(self::TABLE_NAME)
                ->execute()
                ->as_array('id');

            $result = ($result) ? :[];

            Cache::instance()->set(
                $cacheName,
                $result,
                Kohana::config('global/cache.popuper.popupsTemplates')
            );

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
     * @return array|mixed
     * @throws \Kohana_Cache_Exception
     */
    public function getById($id)
    {
        if (!$id) {
            return [];
        }

        $cacheName = "popuper:::popupsTemplates:getById:id:{$id}";

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
                Kohana::config('global/cache.popuper.popupsTemplates')
            );

        }
        return $result;
    }

    /**
     * Function getWithOverlay
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
    public function getWithOverlay($id)
    {
        if (!$id) {
            return [];
        }

        $result = $this->getById($id);

        $overlayId = Arr::get($result, 'overlayId');
        if ($overlayId) {
            $overlayData = (new Overlays())->getById($overlayId);
            $result['overlay'] = $overlayData;
        }

        return $result;
    }
}
