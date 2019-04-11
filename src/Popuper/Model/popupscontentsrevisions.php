<?php
namespace Popuper\Model;

use DB;
use Model;

/**
 * Class PopupsContentsRevisions
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsContentsRevisions extends Model
{
    const TABLE_NAME = 'popupsContentRevisions';

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
            ->columns(['revisionId', 'languageId', 'value']);

        $allLanguages = (new \Model_Language())->getIsoList();
        foreach ($changedData as $langIso => $value) {

            $languageId = array_search($langIso, $allLanguages);
            if ($languageId === false) {
                continue;
            }
            $query->values(
                [
                    'revisionId' => $revisionId,
                    'languageId' => $languageId,
                    'value' => $value,
                ]
            );
        }

        $query->execute();

        return true;

    }
}
