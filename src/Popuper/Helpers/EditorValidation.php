<?php

namespace Popuper\Helpers;

use Popuper\Model\Popups;

/**
 *
 * @author Andrey Fomov <andrey.fomov@tstechpro.com>
 *
 * @package Popuper\Helpers
 */
class EditorValidation
{
    protected static $_allLanguages = [];

    protected static $_lastInvalidAddresses = [];

    protected static $_lastInvalidContentLangs = [];

    /**
     * Function getLastInvalidAddresses
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param string $type
     *
     * @return mixed
     */
    public static function getLastInvalidAddresses($type =' default')
    {

        return \Arr::get(self::$_lastInvalidAddresses,$type ,[]);
    }

    /**
     * Function LastInvalidContentLangs
     * Get $_lastInvalidContentLangs value
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @return array
     */
    public static function getLastInvalidContentLangs()
    {

        return self::$_lastInvalidContentLangs;
    }


    /**
     * Function validateContentByLanguages
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $contentArray
     *
     * @return bool
     */
    public static function validateContentByLanguages($contentArray = [])
    {

        if (!is_array($contentArray)) {
            return false;
        }

        $invalid = [];
        foreach ($contentArray as $lang => $value) {

            if (!static::validateContent($value)) {
                $invalid[$lang] = $value;
            }
        }

        self::$_lastInvalidContentLangs = $invalid;

        return !$invalid;
    }

    /**
     * Function validateContent
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param mixed $content
     *
     * @return bool
     */
    public static function validateContent($content)
    {
        if (!is_string($content)) {
            return false;
        }

        return true;
    }

    /**
     * Function validateAvailableLangInKeys
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $contentByLangs
     *
     * @return bool
     */
    public static function validateAvailableLangInKeys($contentByLangs = [])
    {
        if (!self::$_allLanguages) {
            self::$_allLanguages = \Model_Language::model()->getFlagsList();
        }
        foreach ($contentByLangs as $language => $content) {

            if (!is_string($language)) {
                return false;
            }

            if (!isset(self::$_allLanguages[$language])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Function validateMediaAddressesArray
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param array $addressesArray
     * @param string $type
     *
     * @return bool
     */
    public static function validateMediaAddressesArray($addressesArray = [], $type = 'default')
    {

        if (!is_array($addressesArray)) {
            return false;
        }


        $invalid = [];
        foreach ($addressesArray as $key => $value) {
            if (!self::validateMediaAddress($value)) {
                $invalid[$key] = $value;
            }
        }

        self::$_lastInvalidAddresses[$type] = $invalid;

        return !$invalid;
    }

    /**
     * Function validateMediaAddress
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param mixed $url
     *
     * @return bool
     */
    public static function validateMediaAddress($url)
    {
        if (!$url) {
            return false;
        }

        if (!is_string($url)) {
            return false;
        }

        if (strlen($url) > 255) {
            return false;
        }

        if ($url !== strip_tags($url)) {
            return false;
        }

        $parsedUrl = \URL::parse($url);
        if (empty($parsedUrl['path'])) {
            return false;
        }

        return true;
    }

    /**
     * Function validateTemplateId
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $templateId
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     */
    public static function validateTemplateId($templateId)
    {
        $allTemplates = (new \Popuper\Model\Templates())->getAll();

        return (
            isset($allTemplates[$templateId])
        );
    }

    /**
     * Function htmlAttrId
     *  Check that value contains only: letter, digits, hyphen, underscore, colon, dot(period).
     * Also it must starts with letter
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $str
     * @param bool $utf8
     *
     * @return bool
     */
    public static function htmlAttrId($str, $utf8 = FALSE)
    {
        if ($utf8 === TRUE)
        {
            $regex = '/^[\pL]{1}[-\pL\pN\pZs_:\.]++$/uD';
        }
        else
        {
            $regex = '/^[a-z]{1}[-a-z0-9_ :\.]++$/iD';
        }

        return (bool) preg_match($regex, $str);
    }

    /**
     * Function htmlAttrClass
     *
     *
     * @author Andrey Fomov <andrey.fomov@tstechpro.com>
     *
     * @param $str
     * @param bool $utf8
     *
     * @return bool
     */
    public static function htmlAttrClass($str, $utf8 = FALSE)
    {
        if ($utf8 === TRUE)
        {
            $regex = '/^[-\pL\pN\pZs_:\.]++$/uD';
        }
        else
        {
            $regex = '/^[-a-z0-9_ :\.]++$/iD';
        }

        return (bool) preg_match($regex, $str);
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $title
     * @param $popupId
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     */
    public static function validateUniqueTitle($title, $popupId)
    {
        $popups = Popups::getByCondition(
            [
                ['name', '=', $title],
                ['id', '!=', $popupId],
            ]
        );

        return count($popups) < 1;
    }


    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     *
     * @param $popupId
     *
     * @return bool
     * @throws \Kohana_Cache_Exception
     * @throws \Popuper\Exceptions\Templates
     */
    public static function isPopupIsValid($popupId) {
        /** @var \Popuper\Editor\Single $popup */
        $popup = new \Popuper\Editor\Single($popupId);
        return $popup->isLoaded();
    }

}