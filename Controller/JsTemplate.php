<?php

namespace Controller;

use Arr;
use F;

/**
 * Class Kohana_Controller_JSTemplate
 * Controller to build response as JS.
 * Contains template to use modal windows(pop-ups).
 * Also it has:
 *  $this->template->contentHTML string. Can contains HTML to show in in model window(pop-up)
 *  $this->template->contentJS string. Can contains JS to execute it after cuurent response appalling
 *  $this->template->appliedScripts array. Can contains list of addresses to JS files
 *      which will be additionally applied ith current script
 *  $this->template->appliedStyles array. Can contains list of addresses to CSS files
 *      which will be additionally applied ith current script
 *      true - can not be closed, false - can be closed
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 */
abstract class JSTemplate extends PopuperGeneral
{
    /**
     * @var string
     */
    public $template = 'popups/template_js';

    /**
     * @var string
     */
    public $contentType = 'application/javascript';

    /**
     * @var string
     */
    public $contentHTML = '';

    /**
     * @var string
     */
    public $contentJS = '';

    /**
     * @var array
     */
    public $appliedStyles = [];

    /**
     * @var array
     */
    public $appliedScripts = [];

    /**
     * @var int
     */
    public $typeId = 0;

    /** @var array */
    public $templateSettings = [];

    /**
     * Function before
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @throws \Exception
     */
    public function before()
    {
        if(F::IsAjaxMode()){
            $this->contentType = 'application/json';
        }
        parent::before();
    }

    /**
     * Function after
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     * @return void
     */
    public function after()
    {
        $this->template->typeId = $this->typeId;
        $this->template->appliedStyles = json_encode(
            array_values($this->_checkListOfAdditionalFiles($this->appliedStyles))
        );
        $this->template->appliedScripts = json_encode(
            array_values($this->_checkListOfAdditionalFiles($this->appliedScripts))
        );
        $this->template->contentJS = $this->contentJS;
        $this->template->currentHost = F::getHostName();
        $this->template->popupsLang = $this->_language;
        $this->template->ip = $this->ip;
        $this->template->countryByIP = $this->countryByIP;
        $this->template->contentHTML = $this->contentHTML;
        $this->template->settings = $this->_buildTemplateSettings();

        parent::after();
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @return array
     */
    protected function _buildTemplateSettings()
    {
        $settings = [];
        if($this->templateSettings){
            if($class = Arr::get($this->templateSettings, 'class')){
                $settings['class'] = $class;
            }
            if($overlayColor = Arr::path($this->templateSettings, 'overlay.color')){
                $settings['overlay']['color'] = $overlayColor;
            }
            if($overlayOpacity = Arr::path($this->templateSettings, 'overlay.opacity')){
                $settings['overlay']['opacity'] = $overlayOpacity;
            }
            if($overlayClickJS = Arr::path($this->templateSettings, 'overlay.clickJS')){
                $settings['overlay']['clickJS'] = $overlayClickJS;
            }
        }

        return $settings;
    }

}
