<?php

namespace Popuper\Editor;

/**
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Editor
 */
class ContentContainer
{
    /**
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @var
     */
    protected $_popupId;

    /**
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @var array
     */
    protected $_availableLanguages = [];

    /**
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @var array
     */
    protected $_contentByLangs = [];

    /**
     * Function __construct
     * ContentContainer constructor.
     *
     * @param $_popupId
     *
     * @throws \Exception
     */
    public function __construct($_popupId)
    {

        $this->_popupId = $_popupId;


        $this->_availableLanguages = array_keys(
            \Language::instance()->getManagersLangList()
        );

        $contents = (new \Popuper\Model\Content())
            ->getAllForPopup($this->_popupId);

        $this->_contentByLangs = array_intersect_key(
            $contents,
            array_fill_keys($this->_availableLanguages, '')
        );
    }

    /**
     * Function reloadForPopupId
     * Reload object for new popupId and merge already added content with already stored
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $newPopupId
     *
     * @return void
     * @throws \Kohana_Cache_Exception
     */
    public function reloadForPopupId($newPopupId)
    {
        $this->_popupId = $newPopupId;

        $contents = (new \Popuper\Model\Content())
            ->getAllForPopup($this->_popupId);

        $popupContentStored = array_intersect_key(
            $contents,
            array_fill_keys($this->_availableLanguages, '')
        );
        $this->_contentByLangs = array_replace($popupContentStored, $this->_contentByLangs);

    }

    /**
     * Function ContentByLangs
     * Get _contentByLangs value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public function getContentByLangs()
    {

        return $this->_contentByLangs;
    }

    /**
     * Function ContentByLangs
     * Set _contentByLangs value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $contentByLangs
     */
    public function setContentByLangs(array $contentByLangs)
    {

        $filteredAvailable = array_intersect_key($contentByLangs, array_fill_keys($this->_availableLanguages, ''));

        $this->_contentByLangs = $filteredAvailable;
    }

    /**
     * Function save
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Kohana_Exception
     */
    public function save()
    {

        return (new \Popuper\Model\Content())
            ->setFewLangsForPopup($this->_popupId, $this->_contentByLangs);

    }
}