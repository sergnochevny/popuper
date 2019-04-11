<?php
namespace Popuper\Model;

use DB;

/**
 * Class Callbacks
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class Callbacks
{
    const TABLE_NAME = 'popupsCallbacks';

    /**
     * Function getByPopupId
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     *
     * @return array
     */
    public function getByPopupId($popupId)
    {
        if (!$popupId) {
            return [];
        }

        return  DB::select()
            ->from(self::TABLE_NAME)
            ->where('popupId', '=', $popupId)
            ->execute()
            ->as_array();
    }
}
