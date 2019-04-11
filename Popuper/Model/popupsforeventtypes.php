<?php
namespace Popuper\Model;

use Model;

/**
 * Class PopupsForEventTypes
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Model
 */
class PopupsForEventTypes extends Model
{
    const TABLE_NAME = 'popupsForEventTypes';

    /**
     * Function getPopupsByEvent
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param int $eventTypeId
     * @param bool $isActive
     *
     * @return array
     * @throws \Kohana_Cache_Exception
     */
    public function getPopupsByEvent($eventTypeId, $isActive = true)
    {
        $eventTypeId = (int) $eventTypeId;
        if (!$eventTypeId) {
            return [];
        }

        $cacheName = "popuper:::popupsForEventTypes:gePopupstByEvent:{$eventTypeId}";
        if (!is_null($isActive)) {
            $isActive = (int) $isActive;
            $cacheName .= ":enabled:{$isActive}";
        }

        $result = \Cache::instance()->get($cacheName);
        if (!is_null($result)) {

            return $result;
        }

        $query = \DB::select(
            ['P.id', 'popupId'],
            'PET.order'
        )
            ->from([Popups::TABLE_NAME, 'P'])
            ->join([self::TABLE_NAME, 'PET'])
            ->on('P.id', '=', 'PET.popupId')
            ->on('PET.eventTypeId', '=', \DB::expr("{$eventTypeId}"));

        if (!is_null($isActive)) {
            $query->where('P.isActive', '=', $isActive);
        }

        $query
            ->order_by('PET.order', 'ASC')
            ->order_by('P.id', 'ASC');

        $result = $query->execute()->as_array(null, 'popupId');

        \Cache::instance()->set($cacheName, $result, \Kohana::config('global/cache.popuper.popupsForEventTypes'));

        return $result;
    }

    /**
     * Function getEventTypeByPopup
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param bool $isActive
     *
     * @return array|mixed
     * @throws \Kohana_Cache_Exception
     */
    public function getEventTypeByPopup($popupId, $isActive = true)
    {
        $popupId = (int) $popupId;
        if (!$popupId) {
            return [];
        }

        $cacheName = "popuper:::popupsForEventTypes:getEventTypeByPopup:{$popupId}";
        if (!is_null($isActive)) {
            $isActive = (int) $isActive;
            $cacheName .= ":enabled:{$isActive}";
        }

        $result = \Cache::instance()->get($cacheName);
        if (!is_null($result)) {

            return $result;
        }

        $query = \DB::select(
                'PET.*'
            )
            ->from([self::TABLE_NAME, 'PET'])
            ->join([EventType::TABLE_NAME, 'ET'])
            ->on('ET.id', '=', 'PET.eventTypeId')
            ->join([Popups::TABLE_NAME, 'P'])
            ->on('P.id', '=', 'PET.popupId')
            ->where('PET.popupId', '=', $popupId);

        if (!is_null($isActive)) {
            $query->where('ET.enabled', '=', $isActive);
        }

        $result = ($query->limit(1)->execute()->current()) ? : [];

        \Cache::instance()->set($cacheName, $result, \Kohana::config('global/cache.popuper.popupsForEventTypes'));

        return $result;
    }

    /**
     * Function setPopupForEvent
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $popupId
     * @param $eventTypeId
     * @param null $order
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public function setPopupForEvent($popupId, $eventTypeId, $order = null)
    {

        if (!$popupId || !$eventTypeId) {
            return false;
        }
        $existData = $this->getEventTypeByPopup($popupId);
        if (is_null($order)) {
            $order = \Arr::get($existData, 'order');
            
        }

        /** Nothing was changed */
        if (
            \Arr::get($existData, 'eventTypeId') == $eventTypeId
            && \Arr::get($existData, 'order') == $order
        ) {
            return false;
        }


        if ($existData) {
            \DB::update(self::TABLE_NAME)
                ->set(
                    [
                        'eventTypeId' => $eventTypeId,
                        'order' => (int) $order,
                    ]
                )
                ->where('popupId', '=', $popupId)
                ->execute();
        } else {
            $toInsert = [
                'popupId' => $popupId,
                'eventTypeId' => $eventTypeId,
                'order' => (int) $order,
            ];

            \DB::insert(self::TABLE_NAME)
                ->columns(array_keys($toInsert))
                ->values($toInsert)
                ->execute();
        }

        return true;

    }

    /**
     * Function getMaxOrderForEvent
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return mixed
     * @throws \Kohana_Cache_Exception
     */
    public function getMaxOrderForEvent($eventTypeId)
    {


        $cacheName = "popuper:::popupsForEventTypes:_getMaxOrderFOrEvent:{$eventTypeId}";

        $result = \Cache::instance()->get($cacheName);
        if (!is_null($result)) {
            return $result;
        }

        $result = \DB::select()
            ->from([self::TABLE_NAME, 'PET'])
            ->where('eventTypeId', '=', $eventTypeId)
            ->order_by('order', 'desc')
            ->limit(1)
            ->execute()
            ->get('order');

        \Cache::instance()->set($cacheName, $result, \Kohana::config('global/cache.popuper.popupsForEventTypes'));

        return $result;

    }
}
