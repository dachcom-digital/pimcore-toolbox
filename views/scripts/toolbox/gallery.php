<?php

$assets = $this->toolboxHelper()->getAssetArray( $this->multihref('images')->getElements() );

?>
<div class="row">

    <div class="col-xs-12 col-gallery">

        <?php if( !empty( $assets ) ) { ?>

            <ul class="slick-slider list-unstyled <?= $this->galleryId; ?>-gal responsive-dots <?= $this->checkbox('useLightbox')->isChecked() ? 'light-gallery' : '' ?>" data-as-nav-for=".<?= $this->galleryId; ?>-thumbs" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true">

                <?php foreach ($assets as $asset) { ?>

                    <li class="slide">
                        <?php if( $this->checkbox('useLightbox')->isChecked() ) { ?>
                            <a href="<?= $asset->getThumbnail('lightBoxImage'); ?>" class="item zoom-icon icon-magnifier"></a>
                        <?php } ?>
                        <img src="<?= $asset->getThumbnail('galleryImage') ?>"  />
                    </li>

                <?php } ?>

            </ul>

            <?php if( $this->checkbox('useThumbnails')->isChecked() ) { ?>

                <ul class="slick-slider slick-slider-thumbs list-unstyled <?= $this->galleryId; ?>-thumbs" data-center-mode="true" data-as-nav-for=".<?= $this->galleryId; ?>-gal" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="4" data-dots="false" data-arrows="true">

                    <?php foreach ($assets as $asset) { ?>

                        <li class="slide">
                            <img src="<?= $asset->getThumbnail('galleryThumb') ?>"  />
                        </li>

                    <?php } ?>
                </ul>
            <?php } ?>

        <?php } else { ?>

            <?php if( $this->editmode ) { ?>

                <div class="alert alert-info" style="line-height:20px;">
                    Es wurden keine Bilder für die Galerie gewählt.
                </div>

            <?php } ?>

        <?php } ?>

    </div>

</div>