<?php

namespace Popuper\Conditions\Elements;

use ConditionsTree\Elements\Condition as BaseElement;
use Popuper\Conditions\Maps\ConditionFieldsMap;

class Condition extends BaseElement
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \ConditionsTree\Interfaces\IConditionFieldsNormalize
     */
    protected function _getConditionFieldsMap()
    {
        return new ConditionFieldsMap();
    }

}