<?php

namespace Popuper\Validators;


use Validator\Validators\BaseValidator;

/**
 * Class TestLeadValidator
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class TestLeadValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _rules()
    {
        return [
            ['leadId', 'not_empty'],
            ['leadId', 'digit'],
            ['leadId', 'isLeadId'],            
            ['leadId', 'leadContainsAttributes', [['isTest' => TEST_LEAD_FLAG]]],
            ['popupId', 'not_empty'],
            ['popupId', 'digit'],
            ['popupId','\\Popuper\\Helpers\\EditorValidation::isPopupIsValid']
        ];
    }

    /**
     * @inheritdoc
     */
    public function getValidationErrors($messages = null)
    {
        if (empty($messages)) {
            $messages = 'popuppreview';
        }

        return parent::getValidationErrors($messages);
    }

}