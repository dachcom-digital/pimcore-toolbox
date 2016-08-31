<?php
/*
 * Available Params:
 *
 * $this->mapParams['title']
 * $this->mapParams['street']
 * $this->mapParams['zip']
 * $this->mapParams['city']
 * $this->mapParams['country']
 * $this->mapParams['add']
 *
 */
?>
<div class="info-window">

    <strong><?=$this->mapParams['title']?></strong><br />
    <?=$this->mapParams['street']?><br />
    <?=$this->mapParams['zip']?> <?=$this->mapParams['city']?>
    <?php if ( isset($this->mapParams['add']) && !empty($this->mapParams['add']) ) {?>
        <br /><?=$this->mapParams['add']?>
    <?php } ?>

    <div class="googlemap-routeplanner">
        <a href="https://maps.google.ch/?daddr=<?=$this->mapParams['street']?>, <?=$this->mapParams['zip']?> <?=$this->mapParams['city']?>, <?=$this->mapParams['country']?>" target="_blank"><?=$this->translate('Route planner')?></a>
    </div>

</div>