<?php

namespace Popuper\Conditions\Processors;

use ConditionsTree\Handlers\SaveHandler;
use ConditionsTree\Processors\SaveProcessor as BaseProcessor;
use Popuper\Conditions\Repositories\Repository;

/**
 * Class SaveConditionsProcessor
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class SaveConditionsProcessor extends BaseProcessor
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Popuper\Conditions\Repositories\Repository $repository
     *
     * @return \ConditionsTree\Processors\SaveProcessor
     */
    public static function getInstance(Repository $repository)
    {
        return new static(
            $repository,
            new SaveHandler()
        );
    }

}