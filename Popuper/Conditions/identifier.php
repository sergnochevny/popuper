<?php

namespace Popuper\Conditions;

use ConditionsTree\Converters\Denormalizer;
use ConditionsTree\Providers\ElementsProvider;
use Popuper\Conditions\Helpers\CacheHelper;
use Popuper\Conditions\Processors\PopupsGetterProcessor;
use Popuper\Conditions\Repositories\Repository;
use Popuper\Conditions\Repositories\ViewRepository;

/**
 * Class Identifier
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class Identifier
{
    /** @var PopupsGetterProcessor[] */
    private static $_getPopupProcessor;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     * @param $variables
     *
     * @return mixed
     * @throws \Kohana_Exception
     */
    public static function getPopupByEventType($eventTypeId, $variables)
    {
        if (empty(static::$_getPopupProcessor[$eventTypeId])) {
            static::$_getPopupProcessor[$eventTypeId] = PopupsGetterProcessor::getInstance($eventTypeId);
        }

        return static::$_getPopupProcessor[$eventTypeId]->get($variables);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupId
     *
     * @return mixed
     * @throws \Kohana_Exception
     */
    public static function getPopupConditionsData($popupId)
    {
        $conditionsData = CacheHelper::getByKey($popupId);
        if ($conditionsData === null) {
            $repository = new ViewRepository($popupId);
            $conditionsData = static::_getConditionsFromDB($repository);
            /**
             * denormalize conditions' list to format for front view
             */
            $conditionsData = Denormalizer::getInstance($repository)->denormalize($conditionsData);

            CacheHelper::setByKey($popupId, $conditionsData);
        }

        return $conditionsData;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\Conditions\Repositories\Repository $repository
     *
     * @return array
     * @throws \Kohana_Exception
     */
    protected static function _getConditionsFromDB(Repository $repository)
    {
        return (new ElementsProvider($repository))->load()->getAll();
    }

}