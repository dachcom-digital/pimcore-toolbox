<?php

namespace Toolbox\View\Helper;

class AssetHelper extends \Zend_View_Helper_Abstract {

    const QUEUE_REGISTRY_ID    = 'tb_script_queue';
    const POSITION_REGISTRY_ID = 'tb_script_positions';

    public function assetHelper( )
    {
       return $this;
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
     * Uses the default Zend HeadScript. @fixme: better idea?
     * @param $scriptData
     */
    public function appendScriptSnipped( $scriptData )
    {
        $this->view->headScript()->appendScript( $scriptData );
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
        if(\Zend_Registry::isRegistered(self::QUEUE_REGISTRY_ID))
        {
            $scriptQueue     = \Zend_Registry::get(self::QUEUE_REGISTRY_ID);
            $scriptPositions = \Zend_Registry::get(self::POSITION_REGISTRY_ID);
        }
        else
        {
            $scriptQueue     = array( 'header' => array(), 'footer' => array() );
            $scriptPositions = array();
        }

        $scriptPositions[$name] = count($scriptQueue);
        $scriptQueue[$params['position']][$name] = array(

            'path'              => $path,
            'dependencies'      => $dependencies,
            'params'            => $params,
            'fileType'          => $fileType

        );

        \Zend_Registry::set(self::QUEUE_REGISTRY_ID,  $scriptQueue);
        \Zend_Registry::set(self::POSITION_REGISTRY_ID, $scriptPositions);

        return $this;

    }

    /**
     * @return string
     * @throws \Zend_Exception
     */
    public function getHtmlData()
    {
        if(!\Zend_Registry::isRegistered(self::QUEUE_REGISTRY_ID))
        {
            return '';
        }

        $isFrontEnd = !$this->view->editmode;
        $isBackend = !$isFrontEnd;

        //put each script name in a queue
        $scriptQueue      = \Zend_Registry::get(self::QUEUE_REGISTRY_ID);
        $scriptPositions  = \Zend_Registry::get(self::POSITION_REGISTRY_ID);

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

                if( ($p['showInFrontEnd'] === FALSE && $isFrontEnd ) || ( $p['showInBackend'] === FALSE && $isBackend )  ) {

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

            if(\Pimcore::inDebugMode())
            {
                $htmlData[ $scriptPosition ] = $this->getUncompressedHtml( $scriptPositions, $scriptQueue[$scriptPosition] );
            }
            else
            {
                $htmlData[ $scriptPosition ] = $this->getCompressedHtml( $scriptPositions, $scriptQueue[$scriptPosition] );
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

    private function getCompressedHtml( $scriptPositions, $scriptQueue )
    {
        $html = '';

        return $html;

    }

}