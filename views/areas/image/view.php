<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('image', $this) ?>
<?php }?>
<div class="toolbox-image <?= $this->select('imageAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/image.php') ?>
</div>