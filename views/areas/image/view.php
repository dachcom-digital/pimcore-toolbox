<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('image', $this) ?>
<?php }?>
<div class="toolbox-image <?= $this->select('imageAdditionalClasses')->getData();?>">
    <?= $this->template("toolbox/image.php") ?>
</div>