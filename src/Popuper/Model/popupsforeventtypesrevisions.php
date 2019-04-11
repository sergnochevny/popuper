<?php
namespace Popuper\Model;

use Model;

/**
 * Class PopupsForEventTypesRevisions
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsForEventTypesRevisions extends Model
{
    const TABLE_NAME = 'popupsForEventTypesRevisions';

    /**
     * Function set
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $revisionId
     * @param $eventTypeId
     * @param $order
     *
     * @return bool
     * @throws \Kohana_Exception
     */
    public function set($revisionId, $eventTypeId, $order)
    {

        if (!$revisionId) {
            return false;
        }

        $query = \DB::insert(self::TABLE_NAME)
            ->columns(['revisionId', 'eventTypeId', 'orderValue']);

            $query->values(
                [
                    'revisionId' => $revisionId,
                    'eventTypeId' => $eventTypeId,
                    'orderValue' => $order,
                ]
            );
        $query->execute();

        return true;

    }

}
