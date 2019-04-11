<?php

namespace Popuper\Variables\Dynamic;

/**
 * Class AbstractVariables
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
interface VariablesInterface
{
    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getAllowedVarList();

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getValues();

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param string $variableName
     *
     * @return mixed
     */
    public function getValue($variableName);

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param string $variableName
     * @param mixed $value
     */
    public function setValue($variableName, $value);

    /**
     * Method should contains system variables with description.
     * System variables are not show in CRM.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getSystemVars();

    /**
     * Method should contains variables with description which can be used by users in CRM.
     * Users variables can be used everywhere.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getUsersVars();

    /**
     * All variables with description.
     * Combines variables from getSystemVars() and getUsersVars() methods
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getAllowedVarListWithDescription();

}
