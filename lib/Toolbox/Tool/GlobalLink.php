<?php

namespace Toolbox\Tool;

use Pimcore\Tool;
use Toolbox\Config;

class GlobalLink {

    /**
     * @param $path
     *
     * @return string
     * @throws \Zend_Exception
     */
    public static function parse( $path )
    {
        $currentCountry = NULL;

        if( \Zend_Registry::isRegistered('Website_Country'))
        {
            $currentCountry = \Zend_Registry::get('Website_Country');
        }

        //only parse if country in l10n is active!
        if( !is_null( $currentCountry ) )
        {
            $validLanguages = Tool::getValidLanguages();

            $currentIsoCode = strtolower( $currentCountry );
            $shiftCountry = FALSE;

            $pathCountry = '';
            $globalString = 'global';

            $urlPath = parse_url($path, PHP_URL_PATH);
            $urlPathFragments = explode('/', ltrim($urlPath, '/'));

            //it's a global page, link is correct.
            if( $currentIsoCode === $globalString )
            {
                return $path;
            }

            if( isset($urlPathFragments[0]) )
            {
                $pathElements = explode('-', $urlPathFragments[0]);

                //first needs to be country
                $pathCountry = isset($pathElements[0]) ? $pathElements[0] : NULL;

                //second needs to be language.
                $pathLanguage = isset($pathElements[1]) ? $pathElements[1] : NULL;

                $isValidLanguage = in_array($pathLanguage, $validLanguages);

                //if 2. fragment is invalid language and 1. fragment is valid language, 1. fragment is missing!
                $shiftCountry = $isValidLanguage == FALSE && in_array($pathCountry, $validLanguages);
            }

            //country is missing. add it.
            if( $shiftCountry )
            {
                $path = '/' . $currentIsoCode .'-' . ltrim($path,'/');
            }
            //it's a global page with "global-" in string. change it.
            else if( substr($path, 0, strlen($globalString)+2) === '/' . $globalString . '-')
            {
                $path = substr_replace($path, '/' . $currentIsoCode . '-', 0, strlen($globalString)+2);
            }

            return $path;
        }

        return $path;

    }

}