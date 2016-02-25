<?php

namespace Toolbox\Tools;

use Pimcore\Tool;

class GlobalLink {

    /**
     * @param $page
     *
     * @return mixed
     */
    public static function setPageNavContext( $page, $navigation )
    {

        $realUrl = self::parse( $page->getRealFullPath() );

        $front = \Zend_Controller_Front::getInstance();
        $request = $front->getRequest();

        $currentRequestPath = (string) $request->getPathInfo();

        $page->setUri( $realUrl );

        if( $currentRequestPath == $realUrl )
        {
            $page->setActive(true);
        }
    }

    /**
     * @param $path
     *
     * @return string
     * @throws \Zend_Exception
     */
    public static function parse( $path )
    {
        if( \Zend_Registry::isRegistered('Website_Country'))
        {
            $currentCountry = \Zend_Registry::get('Website_Country');
            $validLanguages = Tool::getValidLanguages();

            //its global
            $currentIsoCode = NULL;

            if( $currentCountry instanceof \CoreShop\Model\Country) {

                $currentIsoCode = strtolower( $currentCountry->getIsoCode() );
            }
            else if( is_string( $currentCountry ) )
            {
                $currentIsoCode = $currentCountry;
            }

            $urlPath = parse_url($path, PHP_URL_PATH);
            $urlPathFragments = explode('/', ltrim($urlPath, '/'));

            //first needs to be country
            $pathCountry = isset($urlPathFragments[0]) ? $urlPathFragments[0] : NULL;

            //second needs to be language.
            $pathLanguage = isset($urlPathFragments[1]) ? $urlPathFragments[1] : NULL;

            $isValidLanguage = in_array($pathLanguage, $validLanguages);

            //if 2. fragment is invalid language and 1. fragment is valid language, 1. fragment is missing!
            $shiftCountry = $isValidLanguage == FALSE && in_array($pathCountry, $validLanguages);

            //country is missing. add it.
            if ($shiftCountry)
            {
                $path = '/' . $currentIsoCode . $path;
            }
            //if country is set, but in wrong context, replace it!
            else if( $pathCountry !== $currentIsoCode )
            {
                $path = '/' . $currentIsoCode . str_replace($pathCountry . '/', '', $path);
            }

            return $path;

        }

        return $path;

    }

}