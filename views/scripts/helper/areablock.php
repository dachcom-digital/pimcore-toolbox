<?php

$name = $this->name ? $this->name : 'default';
$type = $this->type ? $this->type : null;

echo $this->areablock(
    'c' . $name,
    \Toolbox\Tools\Area::getAreaBlockConfiguration( $type )
);