<?php

namespace Popuper\Variables\Dynamic;

/**
 * Class AbstractVariables
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
abstract class AbstractVariables implements VariablesInterface
{
    /**
     * @var array
     */
    protected $_variables = [];

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getAllowedVarList()
    {
        return array_keys($this->getAllowedVarListWithDescription());
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getValues()
    {
        $result = [];
        foreach ($this->getAllowedVarList() as $item) {
            $result[$item] = $this->getValue($item);
        }

        return $result;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param string $variableName
     *
     * @return mixed
     */
    public function getValue($variableName)
    {
        if(isset($this->_variables[$variableName])){
            return $this->_variables[$variableName];
        }

        return null;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param string $variableName
     * @param mixed $value
     */
    public function setValue($variableName, $value)
    {
        $this->_variables[$variableName] = $value;
    }

    /**
     * All variables with description.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getAllowedVarListWithDescription()
    {
        return $this->getUsersVars() + $this->getSystemVars();
    }

    /**
     * Loads values
     */
    protected abstract function _loadValues();

}
