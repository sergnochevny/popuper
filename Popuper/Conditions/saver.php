<?php

namespace Popuper\Conditions;

use ConditionsTree\Converters\NormalizerForUsualFrontIncomeTree as Normalizer;
use Popuper\Conditions\Helpers\CacheHelper;
use Popuper\Conditions\Maps\ConditionViewFieldsMap;
use Popuper\Conditions\Processors\SaveConditionsProcessor;
use Popuper\Conditions\Repositories\ViewRepository;
use Popuper\Conditions\Validator\BatchConditionsValidator;
use Validator\Chains\ContinuousChainValidation;

/**
 * Class Saver
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class Saver
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupId
     *
     * @param array $conditionsData
     *
     * @return mixed
     * @throws \Database_TransactionException
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public static function save($popupId, array $conditionsData)
    {
        CacheHelper::removeByKey($popupId);

        $repository = ViewRepository::getInstance($popupId);
        /**
         * normalize incoming data in a format that will be stored in DB 
         */
        $conditionsData = Normalizer::getInstance($repository)->normalize($conditionsData);

        return SaveConditionsProcessor::getInstance($repository)->save($conditionsData);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     * @param $conditionsData
     *
     * @return mixed
     * @throws \Exception
     */
    public static function validate($eventTypeId, array $conditionsData)
    {
        $conditionsData = static::_addEventIdToData($eventTypeId, $conditionsData);

        return ContinuousChainValidation::getInstance()
            ->next(new BatchConditionsValidator($conditionsData))
            ->validate();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     * @param $conditionsData
     *
     * @return array
     *
     * add to incoming data of eventTypeId's value for validation
     *
     */
    protected static function _addEventIdToData($eventTypeId, array $conditionsData)
    {
        return array_merge(
            [ConditionViewFieldsMap::VALIDATE_EVENT_TYPE_ID => $eventTypeId],
            $conditionsData
        );
    }

}