<div class="row">

    <div class="col-xs-12 col-gallery">

        <?= $this->template('toolbox/gallery/gallery.php', [
            'editmode' => $this->editmode,
            'images' => $this->toolboxHelper()->getAssetArray( $this->multihref('images')->getElements() ),
            'galleryId' => $this->galleryId,
            'useThumbnails' => $this->checkbox('useThumbnails')->isChecked(),
            'useLightbox' => $this->checkbox('useLightbox')->isChecked()
        ]); ?>

    </div>

</div>
