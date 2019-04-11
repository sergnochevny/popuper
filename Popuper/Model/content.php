<?php
namespace Popuper\Model;

use Arr;
use Cache;
use DB;
use Kohana;
use Model;

/**
 * Class Contents
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class Content
{
    const TABLE_NAME = 'popupsContent';

    /**
     * Function get
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int $popupId
     * @param string $lang
     * @param bool $useDefaultForMissed
     *
     * @return mixed|string
     * @throws \Kohana_Cache_Exception
     */
    public function get($popupId, $lang)
    {
        if (!$popupId) {
            return '';
        }

        $cacheName = "popuper:::popupsContent:getId:popupId:{$popupId}:lang:{$lang}";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select('value')
                ->from([self::TABLE_NAME, 'C'])
                ->join([\Model_Language::TABLE_NAME, 'L'])
                ->on('L.id', '=', 'C.languageId')
                ->where('C.popupId', '=', $popupId)
                ->where('L.iso', '=', $lang)
                ->where('C.v', '=', 1)
                ->execute()
                ->get('value', '');

            /** If find not for default alng and no any resuls founded - try to get results by default language */
            $defaultLang = Kohana::config('global/languages')->get('default');

            if (!$result && $lang != $defaultLang) {
                $result = $this->get($popupId, $defaultLang);
            }

            Cache::instance()->set(
                $cacheName,
                $result,
                Kohana::config('global/cache.popuper.popupsContent')
            );

        }

        return $result;
    }

    /**
     * Function getAllForPopup
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     *
     * @return mixed|string
     * @throws \Kohana_Cache_Exception
     */
    public function getAllForPopup($popupId)
    {
        if (!$popupId) {
            return [];
        }

        $cacheName = "popuper:::getAllForPopup:getId:popupId:{$popupId}";

        $result = Cache::instance()->get($cacheName);

        if (is_null($result)) {

            $result = DB::select('L.iso', 'value')
                ->from([self::TABLE_NAME, 'C'])
                ->join([\Model_Language::TABLE_NAME, 'L'])
                ->on('L.id', '=', 'C.languageId')
                ->where('C.popupId', '=', $popupId)
                ->where('C.v', '=', 1)
                ->execute()
                ->as_array('iso', 'value');

            Cache::instance()->set(
                $cacheName,
                $result,
                Kohana::config('global/cache.popuper.popupsContent')
            );

        }

        return $result;
    }

    /**
     * Function setFewLangsForPopup
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $contentByLangs
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public function setFewLangsForPopup($popupId, array $contentByLangs)
    {
        $exist = $this->getAllForPopup($popupId);
        $langIds = array_flip((new \Model_Language())->getIsoList());

        $insert = [];
        $update = [];
        $delete = [];
        foreach ($contentByLangs as $lang => $value) {

            if (!isset($langIds[$lang])) {
                continue;
            }

            $langId = $langIds[$lang];

            if (!isset($exist[$lang])) {
                if ($value) {
                    $insert[$langId] = $value;
                }
                continue;
            }

            if ($exist[$lang] == $value) {
                continue;
            }

            if ($value) {
                $update[$langId] = $value;
            } else {
                $delete[] = $langId;
            }

        }

        if (!$insert && !$update && !$delete) {
            return false;
        }

        return $this->_insertForPopupByLang($popupId, $insert)
               || $this->_updateForPopupByLang($popupId, $update)
               || $this->_deleteForPopupByLang($popupId, $delete);
    }

    /**
     * Function _insertForPopupByLang
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $insertData
     *
     * @return bool
     * @throws \Kohana_Exception
     */
    protected function _insertForPopupByLang($popupId, array $insertData)
    {

        if (!$insertData) {
            return false;
        }

        $query = DB::insert(self::TABLE_NAME)
            ->columns(
                [
                    'popupId',
                    'languageId',
                    'value',
                    'v'
                ]
            );
        foreach ($insertData as $langId => $value) {

            $query->values(
                [
                    $popupId,
                    $langId,
                    $value,
                    '1'
                ]
            );
        }

        $query->execute();

        return true;
    }

    /**
     * Function _updateForPopupByLang
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $updateData
     *
     * @return int
     */
    protected function _updateForPopupByLang($popupId, array $updateData)
    {

        $updatedCount = 0;
        foreach ($updateData as $langId => $value) {
            $updatedCount += DB::update(self::TABLE_NAME)
                ->set(['value' => $value])
                ->where('popupId', '=', $popupId)
                ->where('languageId', '=', $langId)
                ->where('v', '=', 1)
                ->execute();
        }

        return $updatedCount;
    }

    /**
     * Function _deleteForPopupByLang
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param array $langIds
     *
     * @return bool
     */
    protected function _deleteForPopupByLang($popupId, array $langIds)
    {

        if (!$langIds) {
            return false;
        }

        DB::delete(self::TABLE_NAME)
            ->where('popupId', '=', $popupId)
            ->where('languageId', 'in', $langIds)
            ->where('v', '=', 1)
            ->execute();

        return true;
    }
}
