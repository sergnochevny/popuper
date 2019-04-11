<?php

namespace Popuper\Conditions\Repositories;

use ConditionsTree\Maps\ConditionOperatorsMap;
use ConditionsTree\Maps\LogicOperatorsMap;
use ConditionsTree\Repositories\Repository as BaseRepository;
use Database_Query_Builder_Select;
use DB;
use Popuper\Conditions\Elements\ElementsFactory;
use Popuper\Conditions\Maps\AliasesSelectedFieldsMap;
use Popuper\Conditions\Models\ConditionsModel;
use Popuper\Conditions\Models\ElementsModel;
use Popuper\Conditions\Models\EntityTypesModel;
use Popuper\Conditions\Models\GroupsModel;
use Popuper\Model\Popups;
use Popuper\Model\PopupsForEventTypes;

/**
 * Class Repository
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class Repository extends BaseRepository
{
    /** @var mixed
     * mainId - eventTypeId for identification of popup by eventType
     * mainId - popupId for getting of front data and saving
     */
    protected $_mainId;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $mainId
     *
     * @return \Popuper\Conditions\Repositories\Repository
     */
    public static function getInstance($mainId)
    {
        return new static($mainId);
    }

    /**
     * Repository constructor.
     *
     * @param $mainId
     */
    public function __construct($mainId)
    {
        $this->_mainId = $mainId;
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @return \ConditionsTree\Interfaces\IElementsFactory
     */
    public function getElementsFactory()
    {
        return new ElementsFactory();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \ConditionsTree\Models\DBTableModel
     */
    public function createElementsModel()
    {
        return ElementsModel::model();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \ConditionsTree\Models\DBTableModel
     */
    public function createEntityTypesModel()
    {
        return EntityTypesModel::model();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \ConditionsTree\Models\DBTableModel
     */
    public function createGroupsModel()
    {
        return GroupsModel::model();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return \ConditionsTree\Models\DBTableModel
     */
    public function createConditionsModel()
    {
        return ConditionsModel::model();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param \Database_Query_Builder_Select $query
     *
     */
    public function initMainQueryEntity(Database_Query_Builder_Select $query)
    {
        $query->select(['P' . '.id', AliasesSelectedFieldsMap::ELEMENT_POPUP_ID])
            ->from([Popups::TABLE_NAME, 'P'])
            ->join([PopupsForEventTypes::TABLE_NAME, 'PET'])
            ->on('P.id', '=', 'PET.popupId')
            ->where('PET.eventTypeId', '=', DB::expr($this->_mainId))
            ->where('P.isActive', '=', true)
            ->order_by('PET.order', 'ASC')
            ->order_by('P' . '.id', 'ASC');
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public function getReferencedOnMainIdFName()
    {
        return 'PET.popupId';
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    public static function getAllLogicOperators()
    {
        return LogicOperatorsMap::getAvailableOperators();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    public static function getAllConditionsOperators()
    {
        return ConditionOperatorsMap::getAvailableOperators();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return mixed
     */
    public function getMainIdForSaveRootElement()
    {
        return $this->_mainId;
    }
}