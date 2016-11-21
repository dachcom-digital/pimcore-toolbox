<?php $galleryId = 'gallery-' . uniqid(); ?>

<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('gallery', $this) ?>
<?php } ?>

<div class="toolbox-gallery <?= $this->select('galleryAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/gallery.php', array('galleryId' => $galleryId )) ?>
</div>