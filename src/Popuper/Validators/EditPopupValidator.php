<?php

namespace Popuper\Validators;

use Arr;
use Kohana;
use Popuper\Conditions\Saver as PopupsConditionsSaver;
use Validate;

/**
 * Class EditPopupValidator
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class EditPopupValidator
{
    /**
     * @return array
     */
    protected $_validationRules = [
        'name' => [
            [
                'rule' => 'not_empty',
            ],
            [
                'rule' => 'max_length',
                'params' => [255],
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateUniqueTitle',
            ],
        ],
        'templateId' => [
            [
                'rule' => 'not_empty',
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateTemplateId',
            ],
            [
                'rule' => 'digit',
            ],
        ],
        'htmlID' => [
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::htmlAttrId',
            ],
            [
                'rule' => 'max_length',
                'params' => [255],
            ],
            [
                'rule' => 'no_html',
            ],
        ],
        'htmlClass' => [
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::htmlAttrClass',
            ],
            [
                'rule' => 'max_length',
                'params' => [255],
            ],
            [
                'rule' => 'no_html',
            ],
        ],
        'content' => [
            [
                'rule' => 'isArray',
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateAvailableLangInKeys',
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateContentByLanguages',
                'params' => [],
                'resultsCallbackName' => '\\Popuper\\Helpers\\EditorValidation::getLastInvalidContentLangs',
                'resultsCallbackParams' => [],
            ],
        ],
        'stylesCustom' => [
            [
                'rule' => 'isArray',
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateMediaAddressesArray',
                'params' => ['styles'],
                'resultsCallbackName' => '\\Popuper\\Helpers\\EditorValidation::getLastInvalidAddresses',
                'resultsCallbackParams' => ['styles'],
            ],
        ],
        'scriptsCustom' => [
            [
                'rule' => 'isArray',
            ],
            [
                'rule' => '\\Popuper\\Helpers\\EditorValidation::validateMediaAddressesArray',
                'params' => ['scripts'],
                'resultsCallbackName' => '\\Popuper\\Helpers\\EditorValidation::getLastInvalidAddresses',
                'resultsCallbackParams' => ['scripts'],
            ],
        ],
        'conditionsData' => [
            ['rule' => 'not_empty'],
        ],
    ];

    protected $_validationErrors;
    protected $_popupId;
    protected $_eventTypeId;

    /**
     * EditPopupValidator constructor.
     *
     * @param $eventTypeId
     * @param $popupId
     */
    public function __construct($eventTypeId, $popupId)
    {
        $this->_popupId = $popupId;
        $this->_eventTypeId = $eventTypeId;
    }

    /**
     * Function ValidationErrors
     * Get _validationErrors value
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return array
     */
    public function getValidationErrors()
    {
        $readableErrors = [];
        foreach ($this->_validationErrors as $field => $error) {
            $invalidValues = Arr::get($error, 'invalidValues', []);
            $rule = Arr::get($error, 0);
            if($invalidValues){
                foreach ($invalidValues as $key => $invalidValue) {
                    $readableErrors["{$field}-{$key}"] = $this->_getErrorMessage(
                        $field,
                        $rule,
                        [
                            ':key' => $key,
                            ':value' => $invalidValue,
                        ]
                    );
                }
            } else {
                $readableErrors[$field] = $this->_getErrorMessage($field, $rule);
            }
        }

        return $readableErrors;
    }

    /**
     * Function _validate
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     *
     * @param array $data
     *
     * @return bool
     */
    public function validate(array $data)
    {
        $validator = new Validate($data);

        $resultsCallbacks = [];

        foreach ($this->_validationRules as $field => $validationData) {
            foreach ($validationData as $ruleData) {
                $rule = Arr::get($ruleData, 'rule');
                if(!$rule){
                    continue;
                }
                $params = Arr::get($ruleData, 'params', []);
                $params[] = $this->_popupId;
                $validator->rule($field, $rule, $params);

                $resultsCallbackName = Arr::get($ruleData, 'resultsCallbackName', []);
                if($resultsCallbackName){
                    $resultsCallbacks[$field][$rule][$resultsCallbackName] = Arr::get(
                        $ruleData,
                        'resultsCallbackParams',
                        []
                    );
                }
            }
        }

        /** @var bool $result */
        $result = $validator->check();
        $this->_validationErrors = $validator->errors();
        $this->_modifyErrorMessagesAfterValidation($resultsCallbacks);

        return (
            $this->_validateConditionsData(
                Arr::get($data, 'conditionsData')
            )
            && $result
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     *
     * @param $conditionsData
     *
     * @return bool
     */
    protected function _validateConditionsData($conditionsData)
    {
        /** conditions validate */
        if(!($result = PopupsConditionsSaver::validate($this->_eventTypeId, $conditionsData))){
            $this->_validationErrors['conditionsData'] = ['error'];
        }

        return $result;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param array $callbacks
     */
    protected function _modifyErrorMessagesAfterValidation(array $callbacks)
    {
        foreach ($callbacks as $field => $fieldCallbacks) {
            if(!isset($this->_validationErrors[$field])){
                continue;
            }

            foreach ($fieldCallbacks as $rule => $callback) {
                if(
                    !$this->_validationErrors[$field]
                    || !is_array($this->_validationErrors[$field])
                    || Arr::path($this->_validationErrors, "{$field}.0") != $rule
                ){
                    continue;
                }

                foreach ($callback as $callbackName => $callbackParams) {
                    $this->_validationErrors[$field]['invalidValues'] =
                        call_user_func($callbackName, ...$callbackParams);
                }
            }
        }
    }

    /**
     * Function _getErrorMessage
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $field
     * @param $rule
     * @param array $values
     *
     * @return string
     */
    protected function _getErrorMessage($field, $rule, array $values = [])
    {
        $msg = Kohana::message(
            'popupsManagement/edit',
            "{$field}.{$rule}"
        );

        return strtr($msg, $values);
    }

}