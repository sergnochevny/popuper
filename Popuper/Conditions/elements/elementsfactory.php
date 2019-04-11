<?php

namespace Popuper\Conditions\Elements;

use ConditionsTree\Interfaces\IElementsFactory;
use ConditionsTree\Interfaces\IParent;
use Popuper\Conditions\Models\EntityTypesModel;

class ElementsFactory implements IElementsFactory
{
    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $type
     * @param array $element
     *
     * @param \ConditionsTree\Interfaces\IParent $parent
     *
     * @return null|\ConditionsTree\Elements\Element
     */
    public function make($type, array $element, IParent $parent = null)
    {
        $result = null;
        if ($type === EntityTypesModel::ENTITY_TYPE_CONDITION) {
            $result = new Condition($element, $parent);
        } else {
            $result = new Group($element, $parent);
        }

        return $result;
    }

}