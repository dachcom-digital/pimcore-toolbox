<?php

$assets = $this->toolboxHelper()->getAssetArray( $this->multihref("images")->getElements() );

$galId = "gallery-" . uniqid();

?>
<div class="row">

    <div class="col-xs-12 col-gallery">

        <?php if( !empty( $assets ) ) { ?>

            <ul class="slick-slider <?= $galId; ?>-gal" data-as-nav-for=".<?= $galId; ?>-thumbs" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="true" data-arrows="true">

                <?php foreach ($assets as $asset) { ?>

                    <li class="slide">
                        <img src="<?= $asset->getThumbnail("galleryImage") ?>"  />
                    </li>

                <?php } ?>

            </ul>

            <?php if( $this->checkbox('useThumbnails')->isChecked() ) { ?>

                <ul class="slick-slider <?= $galId; ?>-thumbs" data-as-nav-for=".<?= $galId; ?>-gal" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="3" data-dots="false" data-arrows="false">

                    <?php foreach ($assets as $asset) { ?>

                        <li class="slide">
                            <img src="<?= $asset->getThumbnail("galleryThumb") ?>"  />
                        </li>

                    <?php } ?>
                </ul>
            <?php } ?>

        <?php } else { ?>

            <?php if( $this->editmode ) { ?>

                <div class="alert alert-info" style="height: 75px;">
                    Es wurden keine Bilder für die Galerie gewählt.
                </div>

            <?php } ?>

        <?php } ?>

    </div>

</div>