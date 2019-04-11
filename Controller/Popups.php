<?php

namespace Controller;

/**
 * Class Kohana_Controller_Popuper
 * class of general pop-uper calls
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
class Popups extends JSTemplate
{
    use PopupManagerInitTrait;

    /**
     * Function action_get
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Kohana_Cache_Exception
     */
    public function action_get()
    {
        /** @var array $data */
        $data = $this->_popupsManager->getData();

        /** @var string contentHTML */
        $this->templateSettings = $data['settings'];

        /** @var string contentHTML */
        $this->contentHTML = $data['html'];

        /** @var string contentJS */
        $this->contentJS = $data['js'];

        /** @var string contentHTML */
        $this->typeId = $data['typeId'];

        /** @var string appliedScripts */
        $this->appliedScripts = array_merge($this->appliedScripts, $data['scripts']);

        /** @var string appliedStyles */
        $this->appliedStyles = array_merge($this->appliedStyles, $data['styles']);
    }

}
