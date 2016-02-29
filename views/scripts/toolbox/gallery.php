<?php

$assets = $this->toolboxHelper()->getAssetArray( $this->multihref("images")->getElements() );

$galId = "gallery-" . uniqid();

?>
<?php if ($this->editmode && $this->toolboxHelper()->hasAdditionalClasses('gallery')) { ?>

    <div class="alert alert-info form-inline">

        <div class="form-group">
            <label>Zusatz:</label>
        </div>
        <div class="form-group">

            <?php

            $acStore = $this->toolboxHelper()->getConfigArray( 'gallery/additionalClasses', TRUE );
            echo $this->select('galleryAdditionalClasses', array('store' => $acStore, 'width' => 200, 'reload' => true));
            ?>

        </div>

    </div>

<?php } ?>
<div class="row">

    <div class="col-xs-12 col-gallery">

        <div class="toolbox-gallery <?= $this->select('galleryAdditionalClasses')->getData();?>">

            <?php if( !empty( $assets ) ) { ?>

                <ul class="slick-slider list-unstyled <?= $galId; ?>-gal responsive-dots" data-as-nav-for=".<?= $galId; ?>-thumbs" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="1" data-dots="false" data-arrows="true">

                    <?php foreach ($assets as $asset) { ?>

                        <li class="slide">
                            <img src="<?= $asset->getThumbnail("galleryImage") ?>"  />
                        </li>

                    <?php } ?>

                </ul>

                <?php if( $this->checkbox('useThumbnails')->isChecked() ) { ?>

                    <ul class="slick-slider slick-slider-thumbs list-unstyled <?= $galId; ?>-thumbs" data-as-nav-for=".<?= $galId; ?>-gal" data-lazy-load="false" data-fade="false" data-variable-width="false" data-autoplay="false" data-slides-to-show="4" data-dots="false" data-arrows="true">

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

</div>