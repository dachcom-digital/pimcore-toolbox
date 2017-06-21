<?php

namespace Toolbox\Tool;

use Pimcore\Tool;

class GlobalLink
{

    /**
     * @param      $path
     * @param bool $checkRequestUri
     *
     * @return string
     * @throws \Zend_Exception
     */
    public static function parse($path, $checkRequestUri = FALSE)
    {
        $currentCountry = NULL;

        if (\Zend_Registry::isRegistered('Website_Country')) {
            $currentCountry = \Zend_Registry::get('Website_Country');
        } else if ($checkRequestUri) {
            $currentCountry = self::checkRequestUri('country');
        }

        $globalString = 'global';

        //is not global and is not ISO 3166-1.
        if(strtolower($currentCountry) !== $globalString || strlen($currentCountry) !== 2){
            $currentCountry = NULL;
        }

        $currentLanguage = NULL;

        if (\Zend_Registry::isRegistered('Zend_Locale')) {
            $currentLanguage = (string)\Zend_Registry::get('Zend_Locale');
        } else if ($checkRequestUri) {
            $currentLanguage = self::checkRequestUri('language');
        }

        //only parse if country in l10n is active!
        if (is_null($currentCountry)) {
            return $path;
        }

        $validLanguages = Tool::getValidLanguages();

        $currentIsoCode = strtolower($currentCountry);
        $currentLangCode = strtolower($currentLanguage);


        $urlPath = parse_url($path, PHP_URL_PATH);
        $urlPathFragments = explode('/', ltrim($urlPath, '/'));

        //it's a global page, link is correct.
        if ($currentIsoCode === $globalString) {
            return $path;
        }

        if (empty($urlPathFragments)) {
            return $path;
        }

        //explode first path fragment, assuming that the first part is language/country slug
        $pathElements = explode('-', $urlPathFragments[0]);

        //first needs to be country
        $pathCountry = isset($pathElements[0]) ? strtolower($pathElements[0]) : NULL;

        //second needs to be language.
        $pathLanguage = isset($pathElements[1]) ? strtolower($pathElements[1]) : NULL;

        //if 1. fragment is valid language and 2. fragment is invalid language, 1. fragment is missing!
        if (!in_array($pathLanguage, $validLanguages) && in_array($pathCountry, $validLanguages)) {
            return '/' . $currentIsoCode . '-' . ltrim($path, '/');
        }

        //it's a global page with "global-" in string. change it.
        if (substr($path, 0, strlen($globalString) + 2) === '/' . $globalString . '-') {
            return substr_replace($path, '/' . $currentIsoCode . '-', 0, strlen($globalString) + 2);
        }

        //wrong country, right language
        if (!is_null($pathCountry) && $pathCountry !== $currentIsoCode && strtolower($pathLanguage) === $currentLangCode) {
            $path = substr_replace($path, '/' . $currentIsoCode . '-', 0, strlen('/' . $pathCountry . '-'));
        } //right country, wrong language
        else if (!is_null($pathLanguage) && $pathLanguage !== $currentLangCode && (!empty($pathCountry) && $pathCountry === $currentIsoCode)) {
            $path = substr_replace($path, '/' . $currentIsoCode . '-' . $currentLangCode, 0, strlen('/' . $pathCountry . '-' . $pathLanguage));

        }

        return $path;
    }

    private static function checkRequestUri($fragment = 'country')
    {
        $currentFragment = $fragment === 'country' ? 'GLOBAL' : '';

        if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
            $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $urlPathFragments = explode('/', ltrim($urlPath, '/'));

            if (isset($urlPathFragments[0])) {
                $slug = explode('-', $urlPathFragments[0]);

                if (count($slug) === 2) {
                    $currentFragment = $fragment === 'country' ? $slug[0] : $slug[1];
                }
            }
        }

        return $currentFragment;
    }

}