<?php

$name = $this->name ? $this->name : 'default';
$type = $this->type ? $this->type : null;

echo $this->areablock(
    'c' . $name,
    \Toolbox\Tool\Area::getAreaBlockConfiguration( $type )
);