<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('gallery', $this) ?>
<?php }?>
<div class="toolbox-gallery <?= $this->select('galleryAdditionalClasses')->getData();?>">
    <?= $this->template("toolbox/gallery.php") ?>
</div>