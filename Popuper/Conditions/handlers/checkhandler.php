<?php

namespace Popuper\Conditions\Handlers;

use ConditionsTree\Handlers\CheckHandler as BaseHandler;
use ConditionsTree\Interfaces\IElement;
use ConditionsTree\Interfaces\IRoot;

class CheckHandler extends BaseHandler
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \ConditionsTree\Interfaces\IElement $element
     * @param $data
     *
     * @return bool
     */
    protected function _beforeHandling(IElement $element, $data)
    {
        $parent = $element->getParent();
        if ($parent instanceof IRoot) {
            $this->_data = $element->getData();
        }

        return parent::_beforeHandling($element, $data);
    }

}