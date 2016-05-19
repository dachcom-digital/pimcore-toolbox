<?php

    $type = $this->select('type')->getData();

    if($type === 'image')
    {
        $this->template('/toolbox/parallaxContainer/image.php');
    }
    else
    {
        $this->template('/toolbox/parallaxContainer/snippet.php');
    }