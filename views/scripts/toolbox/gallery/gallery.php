
<?php if( !empty( $this->images ) ) { ?>

    <ul class="slick-slider list-unstyled <?= ( !$this->useThumbnails ) ?:'thumbnail-slider' ?> <?= $this->galleryId; ?>-gal responsive-dots <?= $this->useLightbox ? 'light-gallery' : '' ?>" data-as-nav-for=".<?= $this->galleryId; ?>-thumbs" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="<?= ( $this->useThumbnails ) ? 'false':'true' ?>" data-arrows="true">

        <?php foreach ($this->images as $image) { ?>

            <li class="slide item"<?php if( $this->useLightbox ) { ?> data-src="<?= $image->getThumbnail("contentImage"); ?>"<?php } ?>>
                <img src="<?= $image->getThumbnail('galleryImage') ?>"  />
            </li>

        <?php } ?>

    </ul>

    <?php if( $this->useThumbnails ) { ?>

        <ul class="slick-slider slick-slider-thumbs list-unstyled <?= $this->galleryId; ?>-thumbs" data-center-mode="true" data-as-nav-for=".<?= $this->galleryId; ?>-gal" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="5" data-dots="false" data-arrows="true">

            <?php foreach ($this->images as $image) { ?>

                <li class="slide">
                    <img src="<?= $image->getThumbnail('galleryThumb') ?>"  />
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
