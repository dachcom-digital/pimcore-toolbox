<?php

$name = $this->name ? $this->name : 'default';
$type = $this->type ? $this->type : null;

$params = $this->toolboxHelper()->getAvailableBricks( $type );

echo $this->areablock(
    'c' . $name,
    [
        'allowed' => $params['allowed'],
        'params' => $params['additional']
    ]

);