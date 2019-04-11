<?php
namespace Popuper\Model;

use DB;
use Model;

/**
 * Class PopupsAttributesRevisions
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsAttributesRevisions extends Model
{
    const TABLE_NAME = 'popupsAttributesRevisions';

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
            ->columns(['revisionId', 'name', 'value']);

        foreach ($changedData as $name => $value) {

            $query->values(
                [
                    'revisionId' => $revisionId,
                    'name' => $name,
                    'value' => $value,
                ]
            );
        }

        $query->execute();

        return true;

    }
}
