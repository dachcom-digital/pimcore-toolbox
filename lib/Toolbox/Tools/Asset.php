<?php

namespace Toolbox\Tools;

class Asset {

    var $scriptQueue = array( 'header' => '', 'footer' => '' );

    var $scriptPosition = array();

    var $isFrontEnd = FALSE;

    var $isBackEnd = FALSE;

    var $baseUrl = '';

    public function setIsFrontEnd( $isFrontend )
    {
        $this->isFrontEnd = $isFrontend;

        return $this;
    }

    public function setIsBackEnd( $isBackEnd )
    {
        $this->isBackEnd = $isBackEnd;

        return $this;
    }

    public function setBaseUrl( $baseUrl )
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array $nameArray
     * @param array $dependencies
     * @param array $params
     */
    public function appendStylesheetGroup( array $nameArray = array(), $dependencies = array(), $params = array() )
    {
        foreach( $nameArray as $name => $path )
        {
            $this->appendStylesheet( $name, $path, $dependencies, $params);
        }

    }

    /**
     * @param string $name
     * @param string $path
     * @param array  $dependencies
     * @param array  $params
     *
     * @return $this
     */
    public function appendStylesheet( $name = '', $path = '', $dependencies = array(), $params = array() )
    {
        $defaultParams = array(

            'showInFrontEnd' => true,
            'showInBackend' => true,
            'includeInMinify' => true,
            'position' => 'footer',
            'type' => 'text/css',
            'media' => 'screen',
            'rel' => 'stylesheet'

        );

        $params = array_merge( $defaultParams, $params);

        $this->appendFile(

            $name,
            $path,
            $dependencies,
            $params,
            'stylesheet'
        );

        return $this;

    }

    /**
     * @param array $nameArray
     * @param array $dependencies
     * @param array $params
     */
    public function appendScriptGroup( array $nameArray = array(), $dependencies = array(), $params = array() )
    {
        foreach( $nameArray as $name => $path )
        {
            $this->appendScript( $name, $path, $dependencies, $params);
        }

    }

    /**
     * @param string $name
     * @param string $path
     * @param array  $dependencies
     * @param array  $params
     *
     * @return $this
     */
    public function appendScript( $name = '', $path = '', $dependencies = array(), $params = array() )
    {
        $defaultParams = array(

            'showInFrontEnd' => true,
            'showInBackend' => true,
            'includeInMinify' => true,
            'position' => 'footer',
            'type' => 'text/javascript'

        );

        $params = array_merge( $defaultParams, $params);

        $this->appendFile(

            $name,
            $path,
            $dependencies,
            $params,
            'javascript'

        );

        return $this;

    }

    /**
     * @param string $name
     * @param string $path
     * @param array  $dependencies
     * @param array  $params
     * @param        $fileType
     *
     * @return $this
     * @throws \Zend_Exception
     */
    private function appendFile( $name = '', $path = '', $dependencies = array(), $params = array(), $fileType )
    {
        $scriptQueue = $this->scriptQueue;
        $scriptPosition = $this->scriptPosition;

        $scriptPosition[$name . '-' . $fileType] = count($scriptQueue);
        $scriptQueue[$params['position']][$name . '-' . $fileType] = array(

            'path'              => $path,
            'dependencies'      => $dependencies,
            'params'            => $params,
            'fileType'          => $fileType

        );


        $this->scriptQueue = $scriptQueue;
        $this->scriptPosition = $scriptPosition;

        return $this;

    }

    /**
     * @return string
     * @throws \Zend_Exception
     */
    public function getHtmlData()
    {
        if( empty( $this->scriptPosition ) )
        {
            return FALSE;
        }

        $scriptQueue      = $this->scriptQueue;
        $scriptPositions  = $this->scriptPosition;

        if( empty( $scriptQueue ) )
        {
            return FALSE;
        }

        $htmlData = array('header' => '', 'footer' => '' );

        foreach($scriptQueue as $scriptPosition => $scripts)
        {
            if( empty( $scripts ) )
            {
                continue;
            }

            //for every script name
            foreach($scripts as $scriptName => &$details)
            {
                $p = $details['params'];

                if( ($p['showInFrontEnd'] === FALSE && $this->isFrontEnd ) || ( $p['showInBackend'] === FALSE && $this->isBackEnd )  ) {

                    unset( $scriptPositions[ $scriptName] );
                    continue;

                }

                if(is_array($details['dependencies']))
                {
                    $currentPosition = $details['pos'];

                    foreach($details['dependencies'] as $dep) {

                        if(array_key_exists($dep, $scriptPositions) && $scriptPositions[$dep] > $currentPosition) {

                            $scriptPositions[$scriptName] = $details['pos'] = $scriptPositions[$dep] + 1;

                        }
                    }
                }
            }

            asort($scriptPositions);

            unset($scriptName, $position);

            if( \Pimcore::inDebugMode() && $this->isFrontEnd )
            {
                $htmlData[ $scriptPosition ] = $this->getCompressedHtml( $scriptPositions, $scriptQueue[$scriptPosition], $scriptPosition );
            }
            else
            {
                $htmlData[ $scriptPosition ] = $this->getUncompressedHtml( $scriptPositions, $scriptQueue[$scriptPosition] );
            }

        }

        return $htmlData;

    }

    private function getUncompressedHtml( $scriptPositions, $scriptQueue )
    {
        $html = '';

        foreach($scriptPositions as $scriptName => $position)
        {
            $el = $scriptQueue[$scriptName];
            $p = $el['params'];

            if( $el['fileType'] == 'javascript')
            {
                $html .= '<script type="' . $p['type'] . '" src="' . $el['path'] . '"></script>' . PHP_EOL;
            }
            else if( $el['fileType'] == 'stylesheet')
            {
                $html .= '<link href="' . $el['path'] . '" media="' . $p['media'] . '" rel="' . $p['rel'] . '" type="' . $p['type'] . '">' . PHP_EOL;
            }
        }

        return $html;

    }

    private function getCompressedHtml( $scriptPositions, $scriptQueue, $scriptPosition )
    {
        $html = '';

        $jsFiles = array();
        $cssFiles = array();

        $absoluteJs = array();
        $absoluteCss = array();

        $jsFilePaths = array();
        $cssFilePaths = array();

        foreach($scriptPositions as $scriptName => $position)
        {
            $el = $scriptQueue[$scriptName];
            $p = $el['params'];

            if( $el['fileType'] == 'javascript')
            {
                if( $p['includeInMinify'])
                {
                    $jsFiles[] = $el['path'];
                }
                else
                {
                    $jsFilePaths[] = $el['path'];
                }

            }
            else if( $el['fileType'] == 'stylesheet')
            {
                if( $p['includeInMinify'])
                {
                    $cssFiles[] = $el['path'];
                }
                else
                {
                    $cssFilePaths[] = $el['path'];
                }
            }
        }

        foreach( $jsFiles as $jsFile)
        {
            $websitePath = PIMCORE_WEBSITE_PATH;
            $absoluteJs[] = $websitePath . str_replace('/website', '', $jsFile );
        }

        foreach( $cssFiles as $cssFile)
        {
            $websitePath = PIMCORE_WEBSITE_PATH;
            $absoluteCss[] = $websitePath . str_replace('/website', '', $cssFile );
        }

        $jsFileName = 'data-' . $scriptPosition . '.js';
        $cssFileName = 'style-' . $scriptPosition . '.css';

        $min_cacheFileLocking = true;

        //Serve Javascript
        $serveController = new \Toolbox\Controller\Minify\Minify();
        $serveController->setGroup( array('g' => 'js') );

        $serveOptions = array(

            'groupsOnly' => TRUE,
            'debug' => FALSE,
            'quiet' => TRUE,
            'encodeMethod' => '',
            'minApp' => array(
                'groups' => array(
                    'js' => $absoluteJs
                )
            )

        );

        \Minify::setCache( PIMCORE_TEMPORARY_DIRECTORY ,$min_cacheFileLocking );

        $servedJsData = \Minify::serve($serveController, $serveOptions);

        if( $servedJsData['success'] == 'true')
        {
            \Pimcore\File::put(PIMCORE_TEMPORARY_DIRECTORY . '/' . $jsFileName, $servedJsData['content']);
            $jsFilePaths[] = $jsFileName;
        }

        //Serve Css
        $serveController = new \Toolbox\Controller\Minify\Minify();
        $serveController->setGroup( array('g' => 'css') );

        $serveOptions['minApp'] = array(
            'groups' => array(
                'css' => $absoluteCss
            )
        );

        $servedCssData = \Minify::serve($serveController, $serveOptions);

        if( $servedCssData['success'] == 'true')
        {
            \Pimcore\File::put(PIMCORE_TEMPORARY_DIRECTORY . '/' . $cssFileName, $servedCssData['content']);
            $cssFilePaths[] = $cssFileName;
        }

        if( !empty( $cssFilePaths ) )
        {
            $cssFilePaths = array_reverse($cssFilePaths);
            foreach( $cssFilePaths as $cssFilePath)
            {
                $html .= '<link href="' . $this->getFilePath($cssFilePath, 'css'). '" rel="stylesheet" type="text/css">' . PHP_EOL;
            }
        }

        if( !empty( $jsFiles ) )
        {
            $jsFilePaths = array_reverse($jsFilePaths);
            foreach( $jsFilePaths as $jsFilePath)
            {
                $html .= '<script type="text/javascript" src="' . $this->getFilePath($jsFilePath, 'js') . '"></script>' . PHP_EOL;
            }

        }

        return $html;

    }

    private function getFilePath($file, $fileType)
    {
        if( preg_match("~^(?:f|ht)tps?://~i", $file) )
        {
            return $file;
        }
        if( substr( $file, 0, 8 ) === '/website' )
        {
            return $file;
        }
        return $this->baseUrl . '/static/' . $fileType .'/' . $file;

    }
}