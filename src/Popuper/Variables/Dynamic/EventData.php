<?php

namespace Popuper\Variables\Dynamic;

use Arr;
use DB;
use Popuper\Model\Event;
use Popuper\Model\EventVariable;
use Popuper\Model\EventVariableValues;

/**
 * Class EventData
 * @package Popuper\Variables\Dynamic
 * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
 */
class EventData extends AbstractVariables
{
    /** @var array */
    protected $_variables = [];

    /** @var int */
    protected $_eventTypeId;

    /** @var array  */
    protected $_allowed = [];

    /** @var integer */
    protected $_eventId;

    /**
     * EventData constructor.
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param integer $eventId
     * @param integer $eventTypeId
     */
    public function __construct($eventId, $eventTypeId = null)
    {
        $this->_eventId = $eventId;
        $this->_eventTypeId = $eventTypeId;

        $this->_loadValues();
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return integer
     */
    public function getEventTypeId()
    {
        if (!$this->_eventTypeId) {
            /** @var \Database_Result $q */
            $q = DB::select('typeId')->from(Event::TABLE_NAME)
                ->where('id', '=', $this->_eventId)
                ->execute();
            $this->_eventTypeId = $q->get('typeId');
        }
        return $this->_eventTypeId;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return array
     */
    public function getAllowed()
    {
        if (empty($this->_allowed)) {
            $this->_allowed = EventVariable::getAllowedForEvent($this->getEventTypeId());
        }
        return $this->_allowed;
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return array
     */
    public function getSystemVars()
    {
        $result = [];
        foreach ($this->getAllowed() as $varName => $item) {
//            if (EventVariable::TYPE_IS_SYSTEM == $item['isSystem']) {
                $result[$varName] = $item['description'];
//            }
        }
        return $result;
    }

    /**
     * Variables with descriptions.
     * Format:
     * [
     *     'VARIABLE_NAME' => 'Description of the variable',
     *     ...
     * ]
     *
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return array
     */
    public function getUsersVars()
    {
        $result = [];
        foreach ($this->getAllowed() as $varName => $item) {
//                if (EventVariable::TYPE_IS_USER == $item['isSystem']) {
                    $result[$varName] = $item['description'];
//                }
        }
        return $result;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     */
    protected function _loadValues()
    {
        if (!$this->_variables) {
            $varVals = EventVariableValues::getValues($this->_eventId);
            $allowed = $this->getAllowed();
            foreach ($varVals as $value) {
                if (!isset($allowed[$value['systemName']])) {
                    continue;
                }
                if (Arr::get($allowed[$value['systemName']], 'isMultiVal') == EventVariable::TYPE_MULTIPLE_VAL) {
                    $val = Arr::get($this->_variables, $value['systemName'], []);
                    $val[] = $value['value'];
                    $this->_variables[$value['systemName']] = $val;
                } else {
                    $this->_variables[$value['systemName']] = $value['value'];
                }
            }
        }
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_variables;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param $values
     *
     * @return \Popuper\Variables\Dynamic\EventData
     */
    public function setValues(array $values)
    {
        foreach ($values as $variableName => $value) {
            $this->setValue($variableName, $value);
        }

        return $this;
    }

}
