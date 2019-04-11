<?php

namespace Popuper\Variables\Dynamic;

use Arr;
use Model_Variables;

/**
 * Class CustomData
 * @author  Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 * @package Popuper\Variables\Dynamic
 */
class CustomData extends AbstractVariables
{
    const LIVE_CHAT_LINK = 'LIVE_CHAT_LINK';

    /** @var array */
    protected $_customVariables = [];

    /** @var string */
    protected $_lang;

    /**
     * CustomData constructor.
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param string $lang
     */
    public function __construct($lang)
    {
        $this->_lang = $lang;

        $this->_loadValues();
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getSystemVars()
    {
        return [];
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getUsersVars()
    {
        $additional = [self::LIVE_CHAT_LINK => 'Link to the Live Chat plugin'];

        $general = [];
        foreach ($this->getCustomVariables() as $key => $customVariable) {
            $general[$key] = '';
        }

        return array_merge($general, $additional);
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     * @return array
     */
    public function getCustomVariables()
    {
        if(!$this->_customVariables){
            $this->_customVariables = Model_Variables::getVariables($this->_lang);
        }

        return $this->_customVariables;
    }

    /**
     * Method loads default values for variables
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     */
    protected function _loadValues()
    {
        $this->_variables = $this->getCustomVariables();

        $this->_variables[self::LIVE_CHAT_LINK] = '<a href="'
            . Arr::get($this->getCustomVariables(), 'LIVE_CHAT_ADDRESS')
            . '" target="_blank">' . __('support')
            . '</a>';
    }
}
