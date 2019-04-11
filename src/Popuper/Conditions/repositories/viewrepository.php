<?php

namespace Popuper\Conditions\Repositories;

use Database_Query_Builder_Select;
use DB;
use Popuper\Conditions\Maps\AliasesSelectedFieldsMap;
use Popuper\Model\Popups;

/**
 * Class Repository
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
class ViewRepository extends Repository
{
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
            ->where('P.id', '=', DB::expr($this->_mainId));
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return string
     */
    public function getReferencedOnMainIdFName()
    {
        return 'P.id';
    }

}