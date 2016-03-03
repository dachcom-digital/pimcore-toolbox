<?php

namespace Toolbox\Tools;

use Pimcore\Tool;

class GlobalLink {

    /**
     * @param $path
     *
     * @return string
     * @throws \Zend_Exception
     */
    public static function parse( $path, $tryServerVars = FALSE )
    {
        $currentCountry = NULL;

        if( \Zend_Registry::isRegistered('Website_Country'))
        {
            $currentCountry = \Zend_Registry::get('Website_Country');
        }
        else if( $tryServerVars === TRUE )
        {
            if( isset( $_SERVER['REQUEST_URI'] ) && !empty( $_SERVER['REQUEST_URI'] ) )
            {
                $urlPath = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $urlPathFragments = explode('/', ltrim($urlPath, '/'));
                $currentCountry = isset($urlPathFragments[0]) ? $urlPathFragments[0] : NULL;
            }

        }

        if( !is_null( $currentCountry ) )
        {
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