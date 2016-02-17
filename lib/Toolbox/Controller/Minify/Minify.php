<?php

namespace Toolbox\Controller\Minify;

class Minify extends \Minify_Controller_Base {

    var $group = array();

    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function setupSources($options)
    {
        $get = $this->group;

        $cOptions = array_merge(array(
                'allowDirs' => '//',
                'groupsOnly' => FALSE,
                'groups' => array(),
                'noMinPattern' => '@[-\\.]min\\.(?:js|css)$@i'
                // matched against basename
            ), (isset($options['minApp']) ? $options['minApp'] : array()));

        unset($options['minApp']);

        $sources = array();
        $this->selectionId = '';
        $firstMissingResource = NULL;

        if (isset($get['g']))
        {
            // add group(s)
            $this->selectionId .= 'g=' . $get['g'];
            $keys = explode(',', $get['g']);
            if ($keys != array_unique($keys))
            {
                $this->log("Duplicate group key found.");
                return $options;
            }

            foreach ($keys as $key)
            {
                if (!isset($cOptions['groups'][$key]))
                {
                    $this->log("A group configuration for \"{$key}\" was not found");
                    return $options;
                }
                $files = $cOptions['groups'][$key];

                // if $files is a single object, casting will break it
                if (is_object($files))
                {
                    $files = array($files);
                }
                elseif (!is_array($files))
                {
                    $files = (array) $files;
                }
                foreach ($files as $file)
                {
                    if ($file instanceof \Minify_Source)
                    {
                        $sources[] = $file;
                        continue;
                    }
                    if (0 === strpos($file, '//'))
                    {
                        $file = $_SERVER['DOCUMENT_ROOT'] . substr($file, 1);
                    }
                    $realpath = realpath($file);
                    if ($realpath && is_file($realpath))
                    {
                        $sources[] = $this->_getFileSource($realpath, $cOptions);
                    }
                    else
                    {
                        $this->log("The path \"{$file}\" (realpath \"{$realpath}\") could not be found (or was not a file)");
                        if (NULL === $firstMissingResource)
                        {
                            $firstMissingResource = basename($file);
                            continue;
                        }
                        else
                        {
                            $secondMissingResource = basename($file);
                            $this->log("More than one file was missing: '$firstMissingResource', '$secondMissingResource'");
                            return $options;
                        }
                    }
                }

                if ($sources)
                {
                    try
                    {
                        $this->checkType($sources[0]);
                    } catch (Exception $e)
                    {
                        $this->log($e->getMessage());
                        return $options;
                    }
                }
            }
        }
        if ($sources)
        {
            if (NULL !== $firstMissingResource)
            {
                array_unshift($sources, new \Minify_Source(array(
                    'id' => 'missingFile'
                    // should not cause cache invalidation
                ,
                    'lastModified' => 0
                    // due to caching, filename is unreliable.
                ,
                    'content' => "/* Minify: at least one missing file. See " . Minify::URL_DEBUG . " */\n",
                    'minifier' => ''
                )));
            }
            $this->sources = $sources;
        }
        else
        {
            $this->log("No sources to serve");
        }
        return $options;
    }

    /**
     * @param string $file
     * @param array  $cOptions
     *
     * @return Minify_Source
     */
    protected function _getFileSource($file, $cOptions)
    {
        $spec['filepath'] = $file;
        if ($cOptions['noMinPattern'] && preg_match($cOptions['noMinPattern'], basename($file)))
        {
            if (preg_match('~\.css$~i', $file))
            {
                $spec['minifyOptions']['compress'] = FALSE;
            }
            else
            {
                $spec['minifier'] = '';
            }
        }
        return new \Minify_Source($spec);
    }

    protected $_type = NULL;

    /**
     * Make sure that only source files of a single type are registered
     *
     * @param string $sourceOrExt
     *
     * @throws \Exception
     */
    public function checkType($sourceOrExt)
    {
        if ($sourceOrExt === 'js')
        {
            $type = Minify::TYPE_JS;
        }
        elseif ($sourceOrExt === 'css')
        {
            $type = Minify::TYPE_CSS;
        }
        elseif ($sourceOrExt->contentType !== NULL)
        {
            $type = $sourceOrExt->contentType;
        }
        else
        {
            return;
        }
        if ($this->_type === NULL)
        {
            $this->_type = $type;
        }
        elseif ($this->_type !== $type)
        {
            throw new \Exception('Content-Type mismatch');
        }
    }
}
