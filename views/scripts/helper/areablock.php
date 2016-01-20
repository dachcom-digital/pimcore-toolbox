<?php

$excludeBricks = is_array($this->excludeBricks) ? $this->excludeBricks : [];
$extraBricks = is_array($this->extraBricks) ? $this->extraBricks : [];
$name = $this->name ? $this->name : 'default';

$params = $this->toolboxHelper()->getAvailableBricks( $excludeBricks, $extraBricks );

echo $this->areablock('c' . $name, array( 'allowed' => $params['allowed'], 'params' => $params['additional'] ));