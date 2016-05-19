<?php if( $this->editmode ) { ?>
    <?= \Toolbox\Tools\ElementBuilder::buildElementConfig('video', $this) ?>
<?php }?>
<div class="toolbox-video <?= $this->select('videoContainerAdditionalClasses')->getData();?>">
    <?= $this->template('toolbox/video.php') ?>
</div>