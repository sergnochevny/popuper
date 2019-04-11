<?php

namespace Popuper\Builder\Templates\Bottom;

use Popuper\Builder\Templates\AbstractCloseOnCLickTemplate;
use \Popuper\Model\Templates as TemplatesModel;
use View;

/**
 * Class Full
 *
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Builder\Templates\Bottom
 */
class Full extends AbstractCloseOnCLickTemplate
{
    /**
     * @var int
     */
    protected $_id = TemplatesModel::ID_BOTTOM_FULL_CLICK_TO_CLOSE;
}