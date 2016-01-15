<?php

namespace Toolbox\View\Helper;

class FootFile extends \Zend_View_Helper_Abstract {

    const QUEUE_REGISTRY_ID    = 'tb_script_queue';
    const POSITION_REGISTRY_ID = 'tb_script_positions';

    public function footFile( ) {

       return $this;

    }

    /**
     * @param string $name
     * @param string $path
     * @param array  $dependencies
     * @param array  $params
     *
     * @return $this
     */
    public function appendStylesheet( $name = '', $path = '', $dependencies = array(), $params = array()  ) {

        $defaultParams = array(

            'showInFrontEnd' => true,
            'showInBackend' => true,
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
     * @param string $name
     * @param string $path
     * @param array  $dependencies
     * @param array  $params
     *
     * @return $this
     */
    public function appendScript( $name = '', $path = '', $dependencies = array(), $params = array() ) {

        $defaultParams = array(

            'showInFrontEnd' => true,
            'showInBackend' => true,
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

    private function appendFile( $name = '', $path = '', $dependencies = array(), $params = array(), $fileType ) {

        if(\Zend_Registry::isRegistered(self::QUEUE_REGISTRY_ID)) {

            $scriptQueue     = \Zend_Registry::get(self::QUEUE_REGISTRY_ID);
            $scriptPositions = \Zend_Registry::get(self::POSITION_REGISTRY_ID);

        } else {

            $scriptQueue     = array();
            $scriptPositions = array();

        }

        $scriptPositions[$name] = count($scriptQueue);
        $scriptQueue[$name] = array(

            'path'              => $path,
            'dependencies'      => $dependencies,
            'params'            => $params,
            'fileType'          => $fileType

        );

        \Zend_Registry::set(self::QUEUE_REGISTRY_ID,  $scriptQueue);
        \Zend_Registry::set(self::POSITION_REGISTRY_ID, $scriptPositions);

        return $this;

    }

    public function getHtml() {

        if(!\Zend_Registry::isRegistered(self::QUEUE_REGISTRY_ID)) {

            return '';

        }

        $isFrontEnd = !$this->view->editmode;
        $isBackend = !$isFrontEnd;

        //put each script name in a queue
        $scriptQueue      = \Zend_Registry::get(self::QUEUE_REGISTRY_ID);
        $scriptPositions  = \Zend_Registry::get(self::POSITION_REGISTRY_ID);


        //for every script name
        foreach($scriptQueue as $scriptName => &$details) {

            $p = $details['params'];

            if( ($p['showInFrontEnd'] === FALSE && $isFrontEnd ) || ( $p['showInBackend'] === FALSE && $isBackend )  ) {

                unset( $scriptPositions[ $scriptName] );
                continue;

            }

            if(is_array($details['dependencies'])) {

                $currentPosition = $details['pos'];

                foreach($details['dependencies'] as $dep) {

                    if(array_key_exists($dep, $scriptPositions) && $scriptPositions[$dep] > $currentPosition) {

                        $scriptPositions[$scriptName] = $details['pos'] = $scriptPositions[$dep] + 1;

                    }
                }
            }
        }

        asort($scriptPositions);

        $html = '';

        foreach($scriptPositions as $scriptName=>$position) {

            $el = $scriptQueue[$scriptName];
            $p = $el['params'];

            if( $el['fileType'] == 'javascript') {

                $html .= '<script type="' . $p['type'] . '" src="' . $el['path'] . '"></script>' . PHP_EOL;

            } else if( $el['fileType'] == 'stylesheet') {

                $html .= '<link href="' . $el['path'] . '" media="' . $p['media'] . '" rel="' . $p['rel'] . '" type="' . $p['type'] . '">' . PHP_EOL;

            }

        }

        unset($scriptName, $position);

        return $html;

    }

}