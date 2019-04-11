<?php
namespace Popuper\Model;

use Arr;
use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class PopupsRevisions
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsRevisions extends Model
{
    const TABLE_NAME = 'popupsRevisions';

    /**
     * Function set
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $userId
     * @param $popupId
     * @param array $changedData
     *
     * @return mixed
     * @throws \Kohana_Exception
     */
    public function set($userId, $popupId, array $changedData)
    {
        $data = [
            'userId' => $userId,
            'popupId' => $popupId,
            'changedData' => ($changedData) ? json_encode($changedData) : '',
            'changedAt' => DB::expr('NOW()'),
        ];

        list($revisionId,) = DB::insert(self::TABLE_NAME)
            ->columns(array_keys($data))
            ->values($data)
            ->execute();

        return $revisionId;

    }
}
