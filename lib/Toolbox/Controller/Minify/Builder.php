<?php
namespace Toolbox\Controller\Minify;

use MatthiasMullie\Minify;

class Builder {

    function setAssets($assets, $type, $outPutFolder, $outPutFileName)
    {
        $lastModifiedFile = $outPutFolder . '/' . basename($outPutFileName) . '-lm';

        $files = array_combine($assets, array_map("filemtime", $assets));
        arsort($files);
        $latestStoredFile = key($files);
        $latestStoredFileModified = filemtime($latestStoredFile);

        $lastCached = FALSE;

        if(!file_exists($lastModifiedFile))
        {
            file_put_contents($lastModifiedFile, $latestStoredFileModified);
        }
        else
        {
            $lastCached = file_get_contents($lastModifiedFile);
        }

        if( $lastCached == FALSE || $latestStoredFileModified > $lastCached)
        {
            if( $type == 'js')
            {
                $minifier = new Minify\JS();
            }
            else if( $type == 'css')
            {
                $minifier = new Minify\CSS();
            }

            foreach( $assets as $asset)
            {
                $minifier->add($asset);
            }

            $minifier->minify($outPutFolder . '/' . $outPutFileName);

            file_put_contents($lastModifiedFile, $latestStoredFileModified);

        }

        return $outPutFolder . '/' . $outPutFileName;
    }
}