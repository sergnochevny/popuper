<?php
namespace Popuper\Builder;

use Arr;
use Kohana;
use VariablesReplacer\LogicOperator;

/**
 * Class Content
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder
 */
class Content
{

    /** @var int */
    protected $_popupId;

    /** @var array  */
    protected $_preparedVariables = [];

    /** @var string */
    protected $_lang;

    /** @var bool */
    protected $_loaded = false;

    /** @var string  */
    protected $_body = '';

    /** @var array */
    protected $_variables = [];

    /**
     * Function __construct
     * Content constructor.
     *
     * @param int $popupId
     * @param array $variables
     */
    public function __construct($popupId, array $variables = [])
    {
        $this->_popupId = $popupId;
        $this->_variables = $variables;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @param array $val
     */
    public function setVariables(array $val)
    {
        $this->_variables = $val;
    }

    /**
     * @author Oleg Zinchenko <oleg.zinchenko@tstechpro.com>
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * Function getBody
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param bool $processVariables
     *
     * @return string
     * @throws \Exception
     */
    public function getBody($processVariables = true)
    {

        if (!$this->_loaded) {
            $this->load();
        }

        $body = $this->_body;

        if ($processVariables) {

            /** @var string $body Set variables */
            $body = strtr($body, $this->_preparedVariables);

            $body = (new LogicOperator($this->_preparedVariables, $body))->replaceIf();
        }

        return $body;
    }

    /**
     * Function _load
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return void
     * @throws \Exception
     */
    public function load()
    {
        $this->_lang = Arr::get(
            $this->getVariables(),
            'eventData.lang',
            Arr::get(
                $this->getVariables(),
                'lang',
                Kohana::config('global/languages')->get('default')
            )
        );

        $this->_body = (new \Popuper\Model\Content())
            ->get($this->_popupId, $this->_lang);

        $this->_preparedVariables = $this->_getPreparedVariables();
        $this->_loaded = true;
    }

    /**
     * Function _getVariables
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return array
     * @throws \Exception
     */
    protected function _getPreparedVariables()
    {
        $replaceVariables = [];
        foreach ($this->_arraySimplifyLevel($this->getVariables()) as $key => $value) {
            $replaceVariables["[{$key}]"] = $value;
        }

        return $replaceVariables;
    }

    protected function _arraySimplifyLevel($array)
    {
        $result = [];
        foreach ($array as $key => $item) {
            if (!is_array($item)) {
                $result[$key] = $item;
            } elseif (\Arr::is_assoc($item)) {
                $result[$key] = $this->_arraySimplifyLevel($item);
            } else {
                $result[$key] = implode(',', $item);
            }
        }
        $result = \Arr::simplifyLevel($result);
        return $result;
    }
}