<?php

namespace Popuper\Conditions\Processors;

use ConditionsTree\Processors\Processor as BaseProcessor;
use Popuper\Conditions\Handlers\CheckHandler;
use Popuper\Conditions\Maps\AliasesSelectedFieldsMap;
use Popuper\Conditions\Repositories\Repository;

/**
 * Class PopupsGetterProcessor
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class PopupsGetterProcessor extends BaseProcessor
{
    /** @var */
    private $_data;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $eventTypeId
     *
     * @return \Popuper\Conditions\Processors\PopupsGetterProcessor
     */
    public static function getInstance($eventTypeId)
    {
        return new static(
            new Repository($eventTypeId),
            new CheckHandler()
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $data
     *
     * @return array
     * @throws \Kohana_Exception
     */
    public function get($data)
    {
        return $this->handle($data) ? $this->_getProcessingResult() : null;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    protected function _getProcessingResult()
    {
        $data = $this->_handler->getHandledData();

        return $data[AliasesSelectedFieldsMap::ELEMENT_POPUP_ID];
    }

}