<?php
namespace Popuper\Model;

use DB;
use Model;

/**
 * Class PopupsScriptsRevisions
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsScriptsRevisions extends Model
{
    const TABLE_NAME = 'popupsScriptsRevisions';

    /**
     * Function set
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $revisionId
     * @param array $changedData
     *
     * @return bool
     * @throws \Kohana_Exception
     */
    public function set($revisionId, array $changedData)
    {

        if (!$changedData) {
            return false;
        }

        $query = DB::insert(self::TABLE_NAME)
            ->columns(['revisionId', 'order', 'address']);

        foreach ($changedData as $order => $address) {

            $query->values(
                [
                    'revisionId' => $revisionId,
                    'order' => $order,
                    'address' => $address,
                ]
            );
        }

        $query->execute();

        return true;

    }
}
