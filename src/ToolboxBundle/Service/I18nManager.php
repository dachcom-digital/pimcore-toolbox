<?php

namespace ToolboxBundle\Service;

use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Service\Locale;
use Pimcore\Model\Document;

class I18nManager
{
    const GLOBAL_COUNTRY_NAMESPACE = 'global';

    /**
     * @var null|RequestStack
     */
    protected $requestStack;

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var array
     */
    protected $localeFragment = [];

    /**
     * IntlFormatterService constructor.
     *
     * @param $requestStack
     * @param $locale
     */
    public function __construct(RequestStack $requestStack, Locale $locale)
    {
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    /**
     * Valid Paths:
     *
     * /de/test
     * /global-de/test
     * /de-de/test
     *
     * @param string $frontEndPath
     *
     * @return string|bool
     */
    public function checkPath($frontEndPath = NULL)
    {
        $originDocument = $this->requestStack->getMasterRequest()->get(DynamicRouter::CONTENT_KEY);

        if (!$originDocument instanceof Document) {
            return FALSE;
        }

        if ($originDocument instanceof Document\Hardlink\Wrapper\Page) {
            $originDocument = $originDocument->getHardLinkSource();
        } else if ($originDocument instanceof Document\Hardlink\Wrapper\Link) {
            $originDocument = $originDocument->getHardLinkSource();
        }

        //@fixme: get it from another context.
        $currentCountry = $originDocument->getProperty('country');

        //only parse if country in i10n is active!
        if (is_null($currentCountry)) {
            return FALSE;
        }

        if (!$this->locale->hasLocale()) {
            return FALSE;
        }

        $currentCountryCode = strtolower($currentCountry);
        $currentLangCode = strtolower($this->locale->findLocale());

        $urlPath = parse_url($frontEndPath, PHP_URL_PATH);
        $urlPathFragments = explode('/', ltrim($urlPath, '/'));

        //no path given.
        if (empty($urlPathFragments)) {
            return FALSE;
        }

        $localePart = array_shift($urlPathFragments);

        //check if localePart is a valid i18n part
        if ($this->hasI18nContext($localePart)) {

            //explode first path fragment, assuming that the first part is language/country slug
            $pathElements = explode('-', $localePart);

            //invalid i18n format
            if (count($pathElements) !== 2) {
                return FALSE;
            } else if (!$this->isValidLanguage($pathElements[0])) {
                return FALSE;
            } else if (!$this->isValidCountry($pathElements[1])) {
                return FALSE;
            }

        //check if language is valid, otherwise there is no locale context.
        } else if (!$this->isValidLanguage($localePart)) {
            return FALSE;
        }

        if($currentCountryCode !== self::GLOBAL_COUNTRY_NAMESPACE) {
            $this->localeFragment = [$currentLangCode . '-' . $currentCountryCode];
        } else {
            $this->localeFragment = [$currentLangCode];
        }

        $newFrontEndPath = $this->buildLocaleUrl($urlPathFragments);

        //same same. return false.
        if($newFrontEndPath === $frontEndPath) {
            return FALSE;
        }

        //\Pimcore\Logger::err('toolbox: from ' . $frontEndPath . ' => ' . $newFrontEndPath);

        return $newFrontEndPath;
    }

    private function buildLocaleUrl($url = [])
    {
        return '/' . join('/', array_merge($this->localeFragment, $url));
    }

    /**
     * @param $path
     *
     * @return bool
     */
    private function hasI18nContext($path)
    {
        return strpos($path, '-') !== FALSE;
    }

    /**
     * @param $fragment
     *
     * @return bool
     */
    private function isValidLanguage($fragment)
    {
        return in_array($fragment, $this->getValidLanguages());
    }

    /**
     * @notImplemented possible through toolbox context?
     *
     * @param $fragment
     *
     * @return bool
     */
    private function isValidCountry($fragment)
    {
        return TRUE;
    }

    private function getValidLanguages()
    {
        return array_merge([self::GLOBAL_COUNTRY_NAMESPACE], \Pimcore\Tool::getValidLanguages());
    }
}