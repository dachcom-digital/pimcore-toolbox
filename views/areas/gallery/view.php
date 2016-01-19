<?php

$assets = $this->toolboxHelper()->getAssetArray( $this->multihref("images")->getElements() );

$this->rel = "gallery-" . uniqid();

$type = $this->select("type")->getData();
$type = $type ? intval($type) : 4;

?>
<div class="row">

    <div class="col-xs-12 col-gallery">

    <?php if( !empty( $assets ) ) { ?>

        <ul class="slick-slider" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="true" data-arrows="true">

        <?php foreach ($assets as $asset) { ?>

           <li class="slide">
               <img src="<?= $asset->getThumbnail("galleryImage") ?>"  />
           </li>

        <?php } ?>

        </ul>

    <?php } else { ?>

        <?php if( $this->editmode ) { ?>

            <div class="alert alert-info" style="height: 75px;">
                Es wurden keine Bilder für die Galerie gewählt.
            </div>

        <?php } ?>

    <?php } ?>

    </div>


</div>
