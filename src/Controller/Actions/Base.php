<?php

namespace Controller\Actions;

use Arr;
use Controller\PopupManagerInitTrait;

/**
 * Class Controller_Actions_Base
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class ControllerBase extends ControllerAbstract
{
    use PopupManagerInitTrait;

    /**
     * Function action_getNext
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     */
    public function action_getNext()
    {
        /** @var array $data */
        $data = $this->_popupsManager->getData();

        $this->template->result = ['success' => false];
        $result = [];

        if(isset($data['html'])){
            $result['content'] = $data['html'];
        }
        if(isset($data['settings'])){
            $result['settings'] = $data['settings'];
        }
        if(isset($data['js'])){
            $result['contentJs'] = $data['js'];
        }
        if(isset($data['scripts'])){
            $result['scripts'] = array_values($data['scripts']);
        }
        if(isset($data['styles'])){
            $result['styles'] = array_values($data['styles']);
        }
        $result['success'] = !empty($result);
        $result['typeId'] = (int) Arr::get($data, 'typeId');

        $this->template->result = $result;
    }

}