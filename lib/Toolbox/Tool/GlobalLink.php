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
        $orgPath = $path;
        $currentCountry = NULL;

        if (\Zend_Registry::isRegistered('Website_Country')) {
            $currentCountry = \Zend_Registry::get('Website_Country');
        } else if ($checkRequestUri) {
            $currentCountry = self::checkRequestUri('country');
        }

        $globalString = 'global';

        //is not global and is not ISO 3166-1.
        if(strtolower($currentCountry) !== $globalString && strlen($currentCountry) !== 2) {
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

        if (empty($urlPathFragments)) {
            return $path;
        }

        $i18nPart = $urlPathFragments[0];

        //explode first path fragment, assuming that the first part is language/country slug
        $pathElements = explode('-', $i18nPart);

        //first needs to be country
        $pathCountry = isset($pathElements[0]) ? strtolower($pathElements[0]) : NULL;

        //second needs to be language.
        $pathLanguage = isset($pathElements[1]) ? strtolower($pathElements[1]) : NULL;

        //its just the global language page like /de/my-page. add country slug.
        if(in_array($i18nPart, $validLanguages) && $currentIsoCode !== $globalString) {
            $path = '/' . $currentIsoCode . '-' . ltrim($path, '/');
        } //wrong country, right language
        else if (!is_null($pathCountry) && $pathCountry !== $currentIsoCode && $pathLanguage === $currentLangCode && in_array($currentLangCode, $validLanguages)) {
            $path = substr_replace($path, '/' . $currentIsoCode . '-', 0, strlen('/' . $pathCountry . '-'));
        } //right country, wrong language
        else if (!is_null($pathLanguage) && $pathLanguage !== $currentLangCode && (!is_null($pathCountry) && $pathCountry === $currentIsoCode)) {
            $path = substr_replace($path, '/' . $currentIsoCode . '-' . $currentLangCode, 0, strlen('/' . $pathCountry . '-' . $pathLanguage));
        }

        if($orgPath === $path) {
            return $orgPath;
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